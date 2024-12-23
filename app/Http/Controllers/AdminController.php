<?php

namespace App\Http\Controllers;

use App\Exports\AreaVariationExport;
use App\Exports\MultiStreetExport;
use App\Exports\StreetExport;
use App\Exports\UsageAreaVariationExport;
use App\Exports\UsageVariationExport;
use App\Exports\UsageVariationsExport;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Surveyor; // Import Surveyor model
use App\Models\Data;
use App\Models\CBE;
use App\Models\Mis; // Ensure your model is properly imported
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use App\Imports\MISImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function sendTestEmail()
    {
        $details = [
            'title' => 'Test Email from SGT Property Survey',
            'body' => 'This is a test email to verify the mail configuration in Laravel 11.'
        ];

        Mail::to('sheikdawood13579@gmail.com')->send(new TestEmail($details));

        return "Email Sent Successfully!";
    }

    public function dashboard()
    {
        $corporations = CBE::all(); // Fetch all corporations
        $details = Data::all();    // Fetch all details

        $datas = []; // Initialize the datas array

        foreach ($details as $detail) {
            $misCount = DB::table($detail->mis)->count();
            $pointDataCount = DB::table($detail->pointdata)->count();

            // Assuming 'assessment' is a column in the 'pointdata' table
            $connectedCount = DB::table($detail->pointdata)
                ->whereIn('assessment', DB::table($detail->mis)->pluck('assessment'))
                ->count();

            // Collecting the data
            $datas[] = [
                'id' => $detail->id,
                'ward' => $detail->ward,
                'zone' => $detail->zone,
                'corporation' => $detail->corporation_name,
                'miscount' => $misCount,
                'pointdatacount' => $pointDataCount,
                'connected' => $connectedCount,
            ];
        }

        return view('admin.dashboard', compact('corporations', 'datas'));
    }




    public function dataStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg',
            'corporation' => 'required',
            'zone' => 'required',
            'ward' => 'required',
            'polygon' => 'required',
            'point' => 'required',
            'line' => 'required',
            'mis' => 'required|file|mimes:xlsx,xls,csv',
            'extend-right' => 'required',
            'extend-left' => 'required',
            'extend-top' => 'required',
            'extend-bottom' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $corporationID = $request->corporation;
        $cbe = CBE::findOrFail($corporationID);
        $corporation = $cbe->name;
        $zone = $request->zone;
        $ward = $request->ward;
        $tablePrefix = "{$corporation}_{$zone}_{$ward}_";

        // Define tables and their schemas
        $tables = [
            'polygons' => function (Blueprint $table) {
                $table->id();
                $table->string('gisid');
                $table->string('type');
                $table->json('coordinates');
                $table->timestamps();
            },
            // Repeat for other tables...
        ];

        // Create tables dynamically if they don't exist
        foreach ($tables as $name => $schema) {
            if (!Schema::hasTable($tablePrefix . $name)) {
                Schema::create($tablePrefix . $name, $schema);
            }
        }

        // Handle MIS Import
        // if ($request->hasFile('mis')) {
        //     $file = $request->file('mis');
        //     $import = new MisImport($tablePrefix . 'mis');

        //     try {
        //         Excel::import($import, $file);
        //     } catch (\Exception $e) {
        //         Log::error('Import Error: ' . $e->getMessage());
        //         return response()->json(['message' => 'Import failed'], 500);
        //     }
        // }

        // Process GeoJSON files (point, line, polygon)
        $geojsonFiles = ['point', 'line', 'polygon'];
        foreach ($geojsonFiles as $geoFile) {
            if ($request->hasFile($geoFile)) {
                $file = $request->file($geoFile);
                $data = json_decode(file_get_contents($file->getRealPath()), true);

                foreach ($data['features'] as $key => $feature) {
                    DB::table($tablePrefix . "{$geoFile}s")->insert([
                        'gisid' => $feature['properties']['GIS_ID'] ?? $key,
                        'type' => $feature['geometry']['type'] ?? null,
                        'coordinates' => json_encode($feature['geometry']['coordinates']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Save uploaded image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = 'image' . '.' . $imageFile->getClientOriginalExtension();
            $corporationPath = public_path("corporations/{$corporation}/{$zone}/{$ward}");

            if (!File::exists($corporationPath)) {
                File::makeDirectory($corporationPath, 0755, true);
            }

            $imageFile->move($corporationPath, $imageName);
            $imagePath = "corporations/{$corporation}/{$zone}/{$ward}/{$imageName}";
        }

        // Save data to the database
        $data = new Data();
        $data->fill([
            'corporation_id' => $cbe->id,
            'corporation_name' => $corporation,
            'ward' => $ward,
            'zone' => $zone,
            'image' => $imagePath,
            'polygon' => $tablePrefix . 'polygons',
            'line' => $tablePrefix . 'lines',
            'point' => $tablePrefix . 'points',
            'mis' => $tablePrefix . 'mis',
            'qc' => $tablePrefix . 'qc',
            'pointdata' => $tablePrefix . 'pointdata',
            'polygondata' => $tablePrefix . 'buildingdata',
            'extend_left' => $request->extend_left,
            'extend_right' => $request->extend_right,
            'extend_top' => $request->extend_top,
            'extend_bottom' => $request->extend_bottom,
        ]);
        $data->save();

        return response()->json(['message' => 'Success', 'data' => 'Data added successfully']);
    }


    // cbe start
    public function cbe()
    {
        $corporations = CBE::all();
        $datas = Data::all();

        return view("admin.cbe", compact("corporations"));
    }

    public function cbeStore(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:c_b_e_s,email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Create a new corporation using the create method
            $corporation = CBE::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password) // Hash the password
            ]);

            // Fetch all corporations to return
            $corporations = CBE::all();

            return response()->json(['data' => 'Corporation stored successfully!', 'corporations' => $corporations], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function cbeUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $corporation = CBE::findOrFail($request->id);

            // Update the corporation data
            $corporation->name = $request->name;
            $corporation->email = $request->email;
            $corporation->password = Hash::make($request->password);
            $corporation->save();

            // Fetch all corporations to return
            $corporations = CBE::all();

            return response()->json(['data' => 'Corporation updated successfully!', 'corporations' => $corporations], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }
    public function cbeDestroy($id)
    {
        // Find the corporation by ID
        $corporation = CBE::find($id);

        // Check if the corporation exists
        if (!$corporation) {
            return response()->json(['data' => 'Corporation not found.'], 404);
        }

        // Delete the corporation
        $corporation->delete();
        $corporations = CBE::all();
        // Return a success response
        return response()->json(['data' => 'Corporation deleted successfully.', 'corporations' => $corporations], 200);
    }

    //surveyors start
    public function surveyors()
    {
        // Retrieve all surveyors from the database
        $surveyors = Surveyor::all();
        $datas = Data::all();
        // Pass the surveyors data to the view
        return view('admin.surveyors', compact('surveyors', 'datas'));
    }
    public function storeSurveyor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:surveyors,email',
            'mobile' => 'required|string|max:20',
            'data_id' => 'required|string|max:50',
            'password' => 'required|string|min:6', // Adjust as needed
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $surveyor = new Surveyor();
        $surveyor->name = $request->input('name');
        $surveyor->email = $request->input('email');
        $surveyor->mobile = $request->input('mobile');
        $surveyor->data_id = $request->input('data_id');
        $surveyor->password = Hash::make($request->password);    // Hash the password
        $surveyor->password_reset_token = null; // Adjust if you need this field

        // Save the surveyor to the database
        $surveyor->save();
        $surveyors = Surveyor::all();
        // Return a success response
        return response()->json(['message' => 'Surveyor added successfully!', 'surveyors' => $surveyors], 201);
    }
    public function surveyorUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'password' => 'nullable',
            'data_id' => 'required',
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $surveyor = Surveyor::findOrFail($request->id);

            // Update the surveyor data
            $surveyor->name = $request->name;
            $surveyor->email = $request->email;
            $surveyor->mobile = $request->mobile;
            $surveyor->data_id = $request->data_id;
            if ($request->password) {
                $surveyor->password = Hash::make($request->password);
            }
            $surveyor->save();

            // Fetch all surveyors to return
            $surveyors = Surveyor::all();

            return response()->json(['data' => 'Surveyor updated successfully!', 'surveyors' => $surveyors], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }
    public function surveyorDestroy($id)
    {
        try {
            $surveyor = Surveyor::findOrFail($id);
            $surveyor->delete();
            $surveyors = Surveyor::all();
            return response()->json(['message' => 'Surveyor deleted successfully!', 'surveyors' => $surveyors], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the surveyor. Please try again.'], 500);
        }
    }
    private function usageVariations($data)
    {
        $allDatas = DB::table($data->mis)
            ->join($data->pointdata, "{$data->mis}.assessment", '=', "{$data->pointdata}.assessment")
            ->join("{$data->polygondata} as polyd", 'polyd.gisid', '=', "{$data->pointdata}.point_gisid")
            ->select(
                "{$data->pointdata}.point_gisid",
                "polyd.road_name",
                "{$data->pointdata}.assessment",
                "{$data->pointdata}.old_assessment",
                "{$data->mis}.building_usage",
                "{$data->pointdata}.bill_usage",
                "{$data->pointdata}.owner_name",
                "{$data->pointdata}.floor",
                "{$data->pointdata}.phone_number",
                "{$data->mis}.plot_area",
                "{$data->mis}.halfyeartax",
                "{$data->mis}.balance"
            )
            ->orderBy("{$data->mis}.road_name")
            ->get();

        // Filter the data based on the condition
        $filter = $allDatas->filter(function ($allData) {
            $buildingUsage = strtoupper($allData->building_usage);
            $billUsage = strtoupper($allData->bill_usage);

            return ($buildingUsage === 'RESIDENTIAL' && in_array($billUsage, ['COMMERCIAL', 'MIXED'])) ||
                ($buildingUsage === 'COMMERCIAL' && $billUsage === 'MIXED');
        });
        return $filter;
    }
    //Usage variation
    public function usageVariation($id)
    {
        $data = Data::findOrFail($id);

        if (is_null($data->mis) || is_null($data->pointdata)) {
            return response()->json(['error' => 'Invalid table names'], 400);
        }

        // Get the data by joining tables

        $filters = $this->usageVariations($data);
        return Excel::download(new UsageVariationExport($filters->toArray(), ''), 'usage_variation.xlsx');
    }

    private function calculatePolygonAreaInSquareFeet($coordinates)
    {
        // Validate the coordinates structure
        if (!is_array($coordinates) || count($coordinates) === 0 || !is_array($coordinates[0])) {
            return 0; // Invalid input
        }

        $numPoints = count($coordinates[0]); // Number of vertices in the polygon
        $area = 0;

        // Ensure there are at least 3 points to form a polygon
        if ($numPoints < 3) return 0;

        for ($i = 0; $i < $numPoints; $i++) {
            // Validate each point
            if (!is_array($coordinates[0][$i]) || count($coordinates[0][$i]) !== 2) {
                return 0; // Invalid point
            }

            // Ensure x and y are numeric
            $x = floatval($coordinates[0][$i][0]);
            $y = floatval($coordinates[0][$i][1]);

            // Calculate area using the shoelace formula
            $nextIndex = ($i + 1) % $numPoints; // Wrap around to the first point
            $xNext = floatval($coordinates[0][$nextIndex][0]);
            $yNext = floatval($coordinates[0][$nextIndex][1]);

            $area += ($x * $yNext) - ($y * $xNext);
        }

        // The area is positive, and we divide by 2 to get the final area
        $area = abs($area) / 2;

        return $area; // The area is in square feet assuming input coordinates are in feet
    }

    private function areaVariations($data)
    {
        $polygons = DB::table($data->polygon)->select('gisid', 'coordinates')->get();

        $allDatas = DB::table("{$data->pointdata} as pd")
            ->join("{$data->polygon} as poly", 'poly.gisid', '=', 'pd.point_gisid')
            ->join("{$data->polygondata} as polyd", 'polyd.gisid', '=', 'pd.point_gisid')
            ->join("{$data->mis} as mis", 'mis.assessment', '=', 'pd.assessment')
            ->select(
                'pd.point_gisid',
                'poly.coordinates',
                'polyd.basement',
                'polyd.road_name',
                'pd.assessment',
                'pd.old_assessment',
                'mis.building_usage as misusage',
                'pd.bill_usage',
                'pd.old_door_no',
                'pd.new_door_no',
                'pd.owner_name',
                'pd.floor',
                'pd.phone_number',
                'mis.plot_area',
                'mis.old_door_no as misdoorno',
                'mis.halfyeartax',
                'mis.balance',
                'polyd.number_floor',
                'polyd.percentage'
            )
            ->orderBy('mis.road_name')
            ->get();

        foreach ($allDatas as $allData) {
            $coordinates = json_decode($allData->coordinates, true);
            $areaInSquareFeet = $this->calculatePolygonAreaInSquareFeet($coordinates) * 10.7639;

            if (is_numeric($areaInSquareFeet)) {
                $number_floor = is_numeric($allData->number_floor) ? (float)$allData->number_floor : 0;
                $basement = is_numeric($allData->basement) ? (float)$allData->basement : 0;
                $percentage = is_numeric($allData->percentage) ? (float)$allData->percentage / 100 : 0;

                $allData->totaldronearea = $areaInSquareFeet * ($number_floor + $basement + $percentage);

                // Calculate total plot area for the same GISID
                $totalPlotArea = array_reduce($allDatas->toArray(), function ($carry, $item) use ($allData) {
                    if ($item->point_gisid === $allData->point_gisid) {
                        $carry += $item->plot_area;
                    }
                    return $carry;
                }, 0);

                $allData->plotcount = $totalPlotArea;
                $allData->dronearea = $areaInSquareFeet;
                $allData->areavariation = $allData->totaldronearea - $totalPlotArea;
                $allData->zone = $data->zone;
                $allData->ward = $data->ward;
            }
        }



        return $allDatas;
    }

    // Area variation
    public function areaVariation($id)
    {
        $data = Data::findOrFail($id);

        if (is_null($data->mis) || is_null($data->pointdata) || is_null($data->polygon)) {
            return response()->json(['error' => 'Invalid table names'], 400);
        }

        // Fetch the data by joining tables
        $filters = $this->areaVariations($data);

        return Excel::download(new AreaVariationExport($filters->toArray(), ''), 'Area_variation.xlsx');
    }
    // $matchingPoints = $pointdata->where('point_gisid', $polygon->gisid);
    // foreach ($matchingPoints as $point) {
    //     $combinedData = array_merge((array)$polygon, (array)$point);

    //     if ($combinedData['areavariation'] > 150) {
    //         if ($combinedData['areavariation'] > 250) {
    //             if (!in_array($combinedData['building_type'], ['Flat', 'apartment', 'Flat-Multistoried'])) {
    //                 $areavariation[] = $combinedData;
    //             }
    //         } elseif ($combinedData['building_usage'] === 'commercial') {
    //             $areavariation[] = $combinedData;
    //         }
    //     }
    // }

    public function usageAndAreaVariation($id)
    {
        $data = Data::findOrFail($id);

        // Validate table names
        if (empty($data->mis) || empty($data->pointdata) || empty($data->polygon)) {
            return response()->json(['error' => 'Invalid table names'], 400);
        }

        try {
            // Fetch variations
            $areaVariation = $this->areaVariations($data)->toArray();
            $usageVariation = $this->usageVariations($data)->toArray();
            $misRoadNames = DB::table($data->mis)->pluck('road_name');

            // Setup export directory
            $exportDir = storage_path('app/public/exports');
            $exportDirZip = storage_path('app/public/exports.zip');
            if (File::exists($exportDir)) {
                File::deleteDirectory($exportDir);
            }
            File::makeDirectory($exportDir, 0755, true);

            $exportCount = 0;
            foreach ($misRoadNames as $misRoadName) {
                // Filter variations
                $filteredUsage = array_filter($usageVariation, fn($item) => $item->road_name === $misRoadName);
                $filteredArea = array_filter($areaVariation, fn($item) => $item->road_name === $misRoadName);

                if (!empty($filteredUsage) || !empty($filteredArea)) {
                    $filePath = "exports/{$misRoadName}_UsageAreaVariation.xlsx";
                    Excel::store(new UsageAreaVariationExport($filteredUsage, $filteredArea, $misRoadName), $filePath, 'public');
                    $exportCount++;
                }
            }


            $zip = new ZipArchive;
            $zipFileName = 'exports.zip';
            $zipFilePath = storage_path("app/public/{$zipFileName}");

            if (file_exists($zipFilePath)) {
                unlink($zipFilePath); // Delete existing zip file if it exists
            }

            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $exportPath = storage_path('app/public/exports');
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($exportPath),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $file) {
                    if (!$file->isDir()) {
                        $zip->addFile($file->getRealPath(), 'exports/' . substr($file->getRealPath(), strlen($exportPath) + 1));
                    }
                }

                $zip->close();

                // Clean up the exported files
                foreach (File::allFiles($exportPath) as $file) {
                    File::delete($file);
                }

                return response()->download($zipFilePath)->deleteFileAfterSend(true);
            } else {
                return response()->json(['error' => 'Could not create zip file'], 500);
            }
        } catch (\Exception $e) {
            Log::error("Error exporting usage and area variations: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred during export.'], 500);
        }
    }


    public function polygonDownload($id)
    {
        $data = Data::findOrFail($id);

        $corporation = $data->corporation_name;
        $zone = $data->zone;
        $ward = $data->ward;

        $tablePrefix = "{$corporation}_{$zone}_{$ward}_";

        // Fetch polygons from the database
        $polygons = DB::table($tablePrefix . 'polygons')->get();

        // Convert polygons to GeoJSON features
        $features = $polygons->map(function ($polygon) {
            $coordinates = json_decode($polygon->coordinates, true);

            if (!$coordinates) {
                return null; // Skip invalid polygons
            }

            return [
                'type' => 'Feature',
                'properties' => [
                    'OBJECTID' => $polygon->id,
                    'GIS_ID' => $polygon->gisid,
                ],
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => $coordinates,
                ],
            ];
        })->filter(); // Remove null entries

        // Prepare GeoJSON structure
        $geojson = [
            'type' => 'FeatureCollection',
            'name' => 'qGISGEOJSON',
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:EPSG::3857',
                ],
            ],
            'features' => $features,
        ];

        $headers = [
            'Content-Type' => 'application/geo+json',
            'Content-Disposition' => 'attachment; filename="' . $tablePrefix . 'polygons.geojson"',
        ];

        return response()->json($geojson, 200, $headers);
    }

    public function pointDownload($id)
    {
        $data = Data::findOrFail($id);

        $corporation = $data->corporation_name;
        $zone = $data->zone;
        $ward = $data->ward;

        $tablePrefix = "{$corporation}_{$zone}_{$ward}_";

        // Fetch points and their associated pointdata in a single query
        $points = DB::table($tablePrefix . 'points')->get();
        $pointdataAll = DB::table($tablePrefix . 'pointdata')->get()->groupBy('point_gisid');

        // Convert points to GeoJSON features
        $features = $points->flatMap(function ($point) use ($pointdataAll) {
            $coordinates = json_decode($point->coordinates, true);

            if (!$coordinates) {
                return []; // Skip invalid points
            }

            $pointdataList = $pointdataAll->get($point->gisid, collect());

            return $pointdataList->map(function ($pointdata) use ($point, $coordinates) {
                $properties = (array) $pointdata;
                $properties['OBJECTID'] = $point->id;
                $properties['GIS_ID'] = $point->gisid;

                return [
                    'type' => 'Feature',
                    'properties' => $properties,
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => $coordinates,
                    ],
                ];
            });
        });

        // Prepare GeoJSON structure
        $geojson = [
            'type' => 'FeatureCollection',
            'name' => 'qGISGEOJSON',
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:EPSG::3857',
                ],
            ],
            'features' => $features,
        ];

        $headers = [
            'Content-Type' => 'application/geo+json',
            'Content-Disposition' => 'attachment; filename="' . $tablePrefix . 'points.geojson"',
        ];

        return response()->json($geojson, 200, $headers);
    }
    public function roadDownload($id)
    {
        $data = Data::findOrFail($id);

        $corporation = $data->corporation_name;
        $zone = $data->zone;
        $ward = $data->ward;

        $tablePrefix = "{$corporation}_{$zone}_{$ward}_";

        // Fetch roads from the database
        $roads = DB::table($tablePrefix . 'line')->get();

        // Convert roads to GeoJSON features
        $features = $roads->map(function ($road) {
            $coordinates = json_decode($road->coordinates, true);

            if (!$coordinates) {
                return null; // Skip invalid roads
            }

            return [
                'type' => 'Feature',
                'properties' => [
                    'OBJECTID' => $road->id,
                    'GIS_ID' => $road->gisid,
                    'NAME' => $road->road_name ?? null, // Include additional properties
                ],
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => $coordinates,
                ],
            ];
        })->filter(); // Remove null entries

        // Prepare GeoJSON structure
        $geojson = [
            'type' => 'FeatureCollection',
            'name' => 'qGISGEOJSON',
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:EPSG::3857',
                ],
            ],
            'features' => $features,
        ];

        $headers = [
            'Content-Type' => 'application/geo+json',
            'Content-Disposition' => 'attachment; filename="' . $tablePrefix . 'roads.geojson"',
        ];

        return response()->json($geojson, 200, $headers);
    }

    public function finalData($id)
    {
        $data = Data::findOrFail($id);
        $polygons = DB::table($data->polygon)->select('gisid', 'coordinates')->get();

        $allDatas = DB::table("{$data->pointdata} as pd")
            ->join("{$data->point} as poly", 'poly.gisid', '=', 'pd.point_gisid')
            ->join("{$data->polygondata} as polyd", 'polyd.gisid', '=', 'pd.point_gisid')
            ->join("{$data->mis} as mis", 'mis.assessment', '=', 'pd.assessment')
            ->select(
                'pd.point_gisid',
                'poly.coordinates',
                'polyd.basement',
                'polyd.road_name',
                'pd.assessment',
                'pd.old_assessment',
                'mis.building_usage as misusage',
                'pd.bill_usage',
                'pd.old_door_no',
                'pd.new_door_no',
                'pd.owner_name',
                'pd.floor',
                'pd.phone_number',
                'mis.plot_area',
                'mis.old_door_no as misdoorno',
                'mis.halfyeartax',
                'mis.balance',
                'polyd.number_floor',
                'polyd.percentage'
            )
            ->orderBy('mis.road_name')
            ->get();
    }
    //Usage variation
    public function downloadsteetwise($id)
    {
        $data = Data::findOrFail($id);

        // Group data by road name
        $mis = DB::table($data->mis)
            ->select('road_name', 'old_door_no', 'new_door_no', 'assessment', 'old_assessment', 'owner_name', 'phone', 'building_usage')
            ->orderBy('old_door_no')
            ->get()
            ->groupBy('road_name');
        // Define the folder path
        $folderPath = storage_path('app/public/streetwise_exports');

        // Delete the folder if it exists
        if (File::exists($folderPath)) {
            File::deleteDirectory($folderPath);
        }

        // Recreate the folder
        File::makeDirectory($folderPath, 0755, true);

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        // Loop through each road name and create a file
        foreach ($mis as $roadName => $data) {
            $sanitizedName = preg_replace('/[^A-Za-z0-9 _-]/', '', $roadName);
            $filename = $sanitizedName . '.xlsx';
            Excel::store(new StreetExport($roadName, $data), "{$folderPath}/{$filename}");
        }

        // Return a download link for all files as a ZIP archive
        $zipFile = storage_path('app/public/streetwise_exports.zip');
        if (!$this->createZipArchive($folderPath, $zipFile)) {
            throw new \Exception("Failed to create ZIP file: {$zipFile}");
        }

        // Log success and download the ZIP file
        Log::info("ZIP file created successfully: {$zipFile}");

        return response()->download($zipFile)->deleteFileAfterSend(true);
    }
    private function createZipArchive($source, $destination)
    {
        // Check if source directory exists
        if (!file_exists($source)) {
            throw new \Exception("Source directory does not exist: {$source}");
        }

        // Open the ZIP file
        $zip = new \ZipArchive();
        if ($zip->open($destination, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Cannot open ZIP file for writing: {$destination}");
        }

        // Iterate through files and add to ZIP
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source));
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($source) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Close the ZIP archive
        if (!$zip->close()) {
            throw new \Exception("Failed to close ZIP file: {$destination}");
        }

        return true;
    }
}
