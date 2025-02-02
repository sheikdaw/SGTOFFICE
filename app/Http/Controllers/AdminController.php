<?php

namespace App\Http\Controllers;

use App\Exports\AreaVariationExport;
use App\Exports\AssessmentDetailsExport;
use App\Exports\BuildingDetailsExport;
use App\Exports\missingBillExport;
use App\Exports\MultiStreetExport;
use App\Exports\PointDataExport;
use App\Exports\StreetExport;
use App\Exports\SurveyorsExport;
use App\Exports\UsageAreaVariationExport;
use App\Exports\UsageVariationExport;
use App\Exports\UsageVariationsExport;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Surveyor;
use App\Models\Data;
use App\Models\CBE;
use App\Models\Mis;
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
use Barryvdh\DomPDF\Facade\Pdf;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\NotIn;

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
            $road_names = DB::table($detail->mis)
                ->distinct()
                ->pluck('road_name'); // Ensure unique road names


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
                'road_name' => $road_names
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

        $tables = [
            'polygons' => function (Blueprint $table) {
                $table->id();
                $table->string('gisid');
                $table->string('type');
                $table->json('coordinates');
                $table->timestamps();
            },
            'points' => function (Blueprint $table) {
                $table->id();
                $table->string('gisid');
                $table->string('type');
                $table->json('coordinates');
                $table->timestamps();
            },
            'lines' => function (Blueprint $table) {
                $table->id();
                $table->string('gisid');
                $table->string('type');
                $table->json('coordinates');
                $table->timestamps();
            },
            'mis' => function (Blueprint $table) {
                $table->id();
                $table->string('assessment')->nullable();
                $table->string('old_assessment')->nullable();
                $table->string('number_floor')->nullable();
                $table->string('new_address')->nullable();
                $table->string('building_usage')->nullable();
                $table->string('construction_type')->nullable();
                $table->string('road_name')->nullable();
                $table->string('phone')->nullable();
                $table->string('building_type')->nullable();
                $table->string('ward')->nullable();
                $table->string('owner_name')->nullable();
                $table->string('old_door_no')->nullable();
                $table->string('new_door_no')->nullable();
                $table->string('plot_area')->nullable();
                $table->string('watertax')->nullable();
                $table->string('halfyeartax')->nullable();
                $table->string('balance')->nullable();
                $table->timestamps();
            },
            'pointdata' => function (Blueprint $table) {
                $table->id();
                $table->string('data_id')->nullable();
                $table->string('point_gisid')->nullable();
                $table->string('worker_name')->nullable();
                $table->string('assessment')->nullable();
                $table->string('old_assessment')->nullable();
                $table->string('owner_name')->nullable();
                $table->string('present_owner_name')->nullable();
                $table->string('eb')->nullable();
                $table->string('floor')->nullable();
                $table->string('bill_usage')->nullable();
                $table->string('aadhar_no')->nullable();
                $table->string('ration_no')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('shop_floor')->nullable();
                $table->string('shop_name')->nullable();
                $table->string('shop_owner_name')->nullable();
                $table->string('old_door_no')->nullable();
                $table->string('new_door_no')->nullable();
                $table->string('shop_category')->nullable();
                $table->string('shop_mobile')->nullable();
                $table->string('license')->nullable();
                $table->string('professional_tax')->nullable();
                $table->string('gst')->nullable();
                $table->string('number_of_employee')->nullable();
                $table->string('trade_income')->nullable();
                $table->string('establishment_remarks')->nullable();
                $table->string('remarks')->nullable();
                $table->string('plot_area')->nullable();
                $table->string('water_tax')->nullable();
                $table->string('halfyeartax')->nullable();
                $table->string('balance')->nullable();
                $table->string('building_data_id')->nullable();
                $table->string('qc_area')->nullable();
                $table->string('qc_usage')->nullable();
                $table->string('qc_name')->nullable();
                $table->string('qc_remarks')->nullable();
                $table->string('otsarea')->nullable();
                $table->timestamps();
            },
            'buildingdata' => function (Blueprint $table) {
                $table->id();
                $table->string('data_id')->nullable();
                $table->string('gisid')->nullable();
                $table->string('number_bill')->nullable();
                $table->string('number_shop')->nullable();
                $table->string('number_floor')->nullable();
                $table->string('new_address')->nullable();
                $table->string('liftroom')->nullable();
                $table->string('headroom')->nullable();
                $table->string('overhead_tank')->nullable();
                $table->string('percentage')->nullable();
                $table->string('building_name')->nullable();
                $table->string('building_usage')->nullable();
                $table->string('construction_type')->nullable();
                $table->string('road_name')->nullable();
                $table->string('ugd')->nullable();
                $table->string('rainwater_harvesting')->nullable();
                $table->string('parking')->nullable();
                $table->string('ramp')->nullable();
                $table->string('hoarding')->nullable();
                $table->string('cctv')->nullable();
                $table->string('cell_tower')->nullable();
                $table->string('solar_panel')->nullable();
                $table->string('basement')->nullable();
                $table->string('water_connection')->nullable();
                $table->string('phone')->nullable();
                $table->string('building_type')->nullable();
                $table->string('image')->nullable();
                $table->string('sqfeet')->nullable();
                $table->string('merge')->nullable();
                $table->string('split')->nullable();
                $table->string('worker_name')->nullable();
                $table->string('remarks')->nullable();
                $table->string('corporationremarks')->nullable();
                $table->timestamps();
            },
            'qc' => function (Blueprint $table) {
                $table->id();
                $table->string('gisid')->nullable();
                $table->string('floor')->nullable();
                $table->string('length')->nullable();
                $table->string('breth')->nullable();
                $table->string('qcarea')->nullable();
                $table->string('qcusage')->nullable();
                $table->string('otsarea')->nullable();
                $table->string('qcremarks')->nullable();
                $table->string('qcname')->nullable();
                $table->timestamps();
            }
        ];

        foreach ($tables as $name => $schema) {
            if (!Schema::hasTable($tablePrefix . $name)) {
                Schema::create($tablePrefix . $name, $schema);
            }
        }

        // Handle Import
        $file = $request->file('mis');
        if (!$file) {
            return response()->json(['message' => 'No file uploaded'], 400);
        }
        $import = new MisImport($tablePrefix . 'mis');

        try {
            Excel::import($import, $file);
        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }

        // Handle Point File Upload
        if ($request->hasFile('point')) {
            $pointFile = $request->file('point');
            $pointData = json_decode(file_get_contents($pointFile->getRealPath()), true);

            foreach ($pointData['features'] as $feature) {
                DB::table($tablePrefix . 'points')->insert([
                    'gisid' => $feature['properties']['GIS_ID'] ?? null,
                    'type' => $feature['geometry']['type'] ?? null,
                    'coordinates' => json_encode($feature['geometry']['coordinates']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Handle Line File Upload
        if ($request->hasFile('line')) {
            $lineFile = $request->file('line');
            $lineData = json_decode(file_get_contents($lineFile->getRealPath()), true);
            $count = 0;
            foreach ($lineData['features'] as $feature) {
                DB::table($tablePrefix . 'lines')->insert([
                    'gisid' => $count++,
                    'type' => $feature['geometry']['type'] ?? null,
                    'coordinates' => json_encode($feature['geometry']['coordinates']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        if ($request->hasFile('polygon')) {
            $lineFile = $request->file('polygon');
            $lineData = json_decode(file_get_contents($lineFile->getRealPath()), true);
            $count = 0;

            foreach ($lineData['features'] as $feature) {
                DB::table($tablePrefix . 'polygons')->insert([
                    'gisid' => $count++,
                    'type' => $feature['geometry']['type'] ?? null,
                    'coordinates' => json_encode($feature['geometry']['coordinates'][0]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        // return response()->json(['message' => 'Success', 'data' => 'Data added successfully']);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = 'image' . '.' . $imageFile->getClientOriginalExtension();
            $corporationPath = public_path('corporations/' . $corporation . '/' . $zone . '/' . $ward);

            // Create the directory if it doesn't exist
            if (!File::exists($corporationPath)) {
                File::makeDirectory($corporationPath, 0755, true);
            }

            // Move the image file
            $imageFile->move($corporationPath, $imageName);
            $imagePath = 'corporations/' . $corporation . '/' . $zone . '/' . $ward . '/' . $imageName;
        }

        // Save Data
        $data = new Data();
        $data->corporation_id = $cbe->id;
        $data->corporation_name =   $corporation;
        $data->ward = $request->input('ward');
        $data->zone = $request->input('zone');
        $data->image = $imagePath; // Save the path to the uploaded image
        $data->polygon = $tablePrefix . 'polygons'; // Save the table name for polygons
        $data->line = $tablePrefix . 'lines'; // Save the table name for lines
        $data->point = $tablePrefix . 'points'; // Save the table name for points
        $data->mis = $tablePrefix . 'mis';
        $data->qc = $tablePrefix . 'qc';
        $data->pointdata = $tablePrefix . 'pointdata';
        $data->polygondata = $tablePrefix . 'buildingdata';
        $data->extend_left = $request->extend_left;
        $data->extend_right = $request->extend_right;
        $data->extend_top = $request->extend_top;
        $data->extend_bottom = $request->extend_bottom;
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
    public function surveyorUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|regex:/^[0-9]{10}$/',
            'password' => 'nullable|string|min:8',
            'data_id' => 'required',
            'id' => 'required|exists:surveyors,id',
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

            if ($request->filled('password')) {
                $surveyor->password = Hash::make($request->password);
            }

            $surveyor->save();

            return response()->json(['message' => 'Surveyor updated successfully!', 'surveyor' => $surveyor], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
        if (!is_array($coordinates) || count($coordinates) === 0 || !is_array($coordinates[0])) {
            return 0;
        }

        $numPoints = count($coordinates[0]);
        $area = 0;

        if ($numPoints < 3) return 0;

        for ($i = 0; $i < $numPoints; $i++) {
            if (!is_array($coordinates[0][$i]) || count($coordinates[0][$i]) !== 2) {
                return 0;
            }
            $x = floatval($coordinates[0][$i][0]);
            $y = floatval($coordinates[0][$i][1]);


            $nextIndex = ($i + 1) % $numPoints;
            $xNext = floatval($coordinates[0][$nextIndex][0]);
            $yNext = floatval($coordinates[0][$nextIndex][1]);

            $area += ($x * $yNext) - ($y * $xNext);
        }

        $area = abs($area) / 2;

        return $area;
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
                'polyd.building_type',
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
        $matchingPoints = $allDatas;
        $areavariations = [];
        foreach ($matchingPoints as $allData) {

            if ($allData->areavariation > 150) {
                if ($allData->areavariation > 350) {
                    if (!in_array($allData->building_type, ['Flat', 'apartment', 'Flat-Multistoried'])) {
                        $areavariations[] = $allData;
                    }
                } else if ($allData->bill_usage === 'commercial') {
                    $areavariations[] = $allData;
                }
            }
        }

        return $areavariations;
    }

    // Area variation
    public function areaVariation($id, Request $request)
    {
        // return response()->json($request->all());
        $data = Data::findOrFail($id);

        if (is_null($data->mis) || is_null($data->pointdata) || is_null($data->polygon)) {
            return response()->json(['error' => 'Invalid table names'], 400);
        }

        // Fetch the data by joining tables
        $filters = $this->areaVariations($data);

        return Excel::download(new AreaVariationExport($filters, ''), 'Area_variation.xlsx');
    }


    public function usageAndAreaVariation($id)
    {
        try {
            $data = Data::findOrFail($id);
            if (empty($data->mis) || empty($data->pointdata) || empty($data->polygon)) {
                return response()->json(['error' => 'Invalid table names'], 400);
            }

            $areaVariation = $this->areaVariations($data);
            $usageVariation = $this->usageVariations($data)->toArray();
            $misRoadNames = DB::table($data->mis)->pluck('road_name');

            $exportDir = storage_path('app/public/exports');
            $exportDirZip = storage_path('app/public/exports.zip');

            if (File::exists($exportDir)) {
                File::deleteDirectory($exportDir);
            }
            File::makeDirectory($exportDir, 0755, true);

            foreach ($misRoadNames as $misRoadName) {
                $filteredUsage = array_filter($usageVariation, fn($item) => $item->road_name === $misRoadName);
                $filteredArea = array_filter($areaVariation, fn($item) => $item->road_name === $misRoadName);

                if (!empty($filteredUsage) || !empty($filteredArea)) {
                    $filePath = "exports/{$misRoadName}_UsageAreaVariation.xlsx";
                    Excel::store(new UsageAreaVariationExport($filteredUsage, $filteredArea, $misRoadName), $filePath, 'public');
                }
            }
            return response()->json(['error' => 'Could not create zip file'], 200);
            $zip = new ZipArchive;
            if ($zip->open($exportDirZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($exportDir), \RecursiveIteratorIterator::LEAVES_ONLY);
                foreach ($files as $file) {
                    if (!$file->isDir()) {
                        $zip->addFile($file->getRealPath(), 'exports/' . substr($file->getRealPath(), strlen($exportDir) + 1));
                    }
                }
                $zip->close();
                File::deleteDirectory($exportDir);
                return response()->download($exportDirZip)->deleteFileAfterSend(true);
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
            // Decode coordinates and cast string values to floats
            $coordinates = json_decode($polygon->coordinates);
            $numericCoordinates = array_map(function ($polygonCoordinates) {
                return array_map(function ($coordinatePair) {
                    return [
                        (float) $coordinatePair[0], // Cast to float
                        (float) $coordinatePair[1], // Cast to float
                    ];
                }, $polygonCoordinates);
            }, $coordinates);

            return [
                'type' => 'Feature',
                'properties' => [
                    'OBJECTID' => $polygon->id,
                    'GIS_ID' => $polygon->gisid,
                ],
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => $numericCoordinates, // Use the converted coordinates
                ],
            ];
        });

        // Prepare GeoJSON structure
        $geojson = [
            'type' => 'FeatureCollection',
            'name' => 'qGISGEOJSON',
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:EPSG::3857', // Ensure the correct CRS
                ],
            ],
            'features' => $features,
        ];

        // Set headers to indicate a file download
        $headers = [
            'Content-Type' => 'application/geo+json',
            'Content-Disposition' => 'attachment; filename="polygons.geojson"',
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
            Excel::store(new StreetExport($roadName, $data), "app/public/streetwise_exports/{$filename}");
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
    public function downloadMissingBill($id)
    {
        $data = Data::findOrFail($id);

        return Excel::download(new missingBillExport($data), 'missing_bills.xlsx');
    }
    public function surveyorsCount($id)
    {
        $data = Data::findOrFail($id); // Retrieve the Data record or throw a 404 error if not found.
        $surveyors = Surveyor::where('data_id', $id)->get(); // Retrieve surveyors linked to this data record.

        if ($surveyors->isEmpty()) {
            return response()->json(['message' => 'No surveyors found.'], 404);
        }

        // Validate that the pointdata table exists.
        if (!Schema::hasTable($data->pointdata)) {
            return response()->json(['message' => 'Table not found: ' . $data->pointdata], 404);
        }

        $results = [];

        foreach ($surveyors as $surveyor) {
            // Retrieve counts for the surveyor and data.
            $surveyedDataCount = DB::table($data->pointdata)
                ->where('worker_name', $surveyor->name)
                ->count();
            $connectedCount = DB::table($data->pointdata)
                ->whereIn('assessment', DB::table($data->mis)->pluck('assessment'))
                ->where('worker_name', $surveyor->name)
                ->count();

            $notConnected = $surveyedDataCount -  $connectedCount;

            $results[] = [
                'surveyor' => $surveyor->name,
                'surveyed_count' => $surveyedDataCount,
                'connected_count' => $connectedCount,
                'not_connected_count' => $notConnected,
            ];
        }

        // Export results to Excel.
        return Excel::download(new SurveyorsExport($results), 'surveyors.xlsx');
    }
    public function downloadPointData($id)
    {
        $data = Data::findOrFail($id);

        // Execute the query to fetch the data
        $pointdata = DB::table("{$data->pointdata} as pd")
            ->join("{$data->polygondata} as polyd", 'polyd.gisid', '=', 'pd.point_gisid')
            ->select(
                'pd.id',
                'pd.data_id',
                'pd.point_gisid',
                'polyd.road_name',
                'pd.worker_name',
                'pd.assessment',
                'pd.old_assessment',
                'pd.owner_name',
                'pd.present_owner_name',
                'pd.eb',
                'pd.floor',
                'pd.bill_usage',
                'pd.aadhar_no',
                'pd.ration_no',
                'pd.phone_number',
                'pd.shop_floor',
                'pd.shop_name',
                'pd.shop_owner_name',
                'pd.old_door_no',
                'pd.new_door_no',
                'pd.shop_category',
                'pd.shop_mobile',
                'pd.license',
                'pd.professional_tax',
                'pd.gst',
                'pd.number_of_employee',
                'pd.trade_income',
                'pd.establishment_remarks',
                'pd.remarks',
                'pd.plot_area',
                'pd.water_tax',
                'pd.halfyeartax',
                'pd.balance',
                'pd.building_data_id',
                'pd.qc_area',
                'pd.qc_usage',
                'pd.qc_name',
                'pd.qc_remarks',
                'pd.otsarea',
                'pd.created_at',
                'pd.updated_at'
            )
            ->get();

        // Export the fetched data
        return Excel::download(new AssessmentDetailsExport($pointdata), 'pointdata_' . $data->ward . '.xlsx');
    }


    public function downloadBuildingData($id)
    {
        // Fetch the specific building record
        $data = Data::findOrFail($id);
        $buildingdata = DB::table($data->polygondata)->get();

        // Export the building details to Excel
        return Excel::download(new BuildingDetailsExport($buildingdata), 'building_details_' . $data->ward . '.xlsx');
    }
    public function searchGisid(Request $request)
    {
        $gisid = $request->input('sgisid');
        $dataId = $request->input('id');

        // Fetch related data using surveyor's data_id
        $data = DB::table('data')->find($dataId);
        $dataId = $data->id;

        if (!$data) {
            return redirect()->back()->with('error', 'No data found for the surveyor.');
        }

        $pointdataTable = $data->pointdata;

        // Validate the existence of the table
        if (!Schema::hasTable($pointdataTable)) {
            return redirect()->back()->with('error', 'Invalid point data table.');
        }

        // Fetch point data using GISID or assessment
        $pointData = DB::table($pointdataTable)
            ->where('point_gisid', $gisid)
            ->orWhere('assessment', $gisid)
            ->get();

        if ($pointData->isEmpty()) {
            return view('admin.editassessment', [
                'pointData' => [],
                'data_id' => $dataId,
            ])->with('error', 'No data found for the provided GISID.');
        }

        // Add additional fields to the point data
        $pointData->each(function ($pdata) use ($data) {
            $pdata->val = $data->id;
        });

        return view('admin.editassessment', compact('pointData', 'dataId'));
    }
    public function searchbuildingGisid(Request $request)
    {
        $gisid = $request->input('sgisid');
        $dataId = $request->input('id');

        // Fetch related data using surveyor's data_id
        $data = DB::table('data')->find($dataId);
        $dataId = $data->id;

        if (!$data) {
            return redirect()->back()->with('error', 'No data found for the surveyor.');
        }

        $polygondataTable = $data->polygondata;

        // Validate the existence of the table
        if (!Schema::hasTable($polygondataTable)) {
            return redirect()->back()->with('error', 'Invalid point data table.');
        }

        // Fetch point data using GISID or assessment
        $polygonData = DB::table($polygondataTable)
            ->where('gisid', $gisid)

            ->get();

        if ($polygonData->isEmpty()) {
            return view('admin.editbuilding', [
                'polygonData' => [],
                'data_id' => $dataId,
            ])->with('error', 'No data found for the provided GISID.');
        }

        // Add additional fields to the point data
        $polygonData->each(function ($pdata) use ($data) {
            $pdata->val = $data->id;
        });

        return view('admin.editbuilding', compact('polygonData', 'dataId'));
    }
    public function updatebuildingdata(Request $request)
    {
        $id = $request->id;

        // Access the nested 'val' key and convert it to an integer
        $val = (int)$request->input('data.val');

        // Fetch the corresponding row from the 'data' table
        $data = DB::table('data')->where('id', $val)->first();

        // Check if $data is null
        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $tableName = $data->polygondata;

        // Extract the updated data from the request
        $updatedData = $request->input('data');

        // Validation rules
        $rules = [
            'gisid' => 'required',
            'building_usage' => 'required',
            'building_type' => 'required',
            // 'bill_usage' => 'required',

            'remarks' => 'nullable',
        ];

        // Validate the updated data
        $validator = Validator::make($updatedData, $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Set the updated_at field to the current timestamp
        $updatedData['updated_at'] = Carbon::now();
        unset($updatedData['val']);
        // Ensure created_at is not accidentally overwritten
        unset($updatedData['created_at']);

        // Update the data in the specified table
        try {
            DB::table($tableName)->where('id', $id)->update($updatedData);
            return response()->json(['message' => 'Data updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update data', 'details' => $e->getMessage()], 500);
        }
    }
    public function deleteBuildingData(Request $request)
    {
        $id = $request->id;


        $val = (int)$request->input('data.val');
        $data = DB::table('data')->where('id', $val)->first();

        $tableName = $data->polygondata;
        DB::table($tableName)->where('id', $id)->delete();
        return response()->json(['message' => 'Data deleted successfully'], 200);
    }


    public function updateAssessment(Request $request)
    {
        $id = $request->id;

        // Access the nested 'val' key and convert it to an integer
        $val = (int)$request->input('data.val');

        // Fetch the corresponding row from the 'data' table
        $data = DB::table('data')->where('id', $val)->first();

        // Check if $data is null
        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $tableName = $data->pointdata;

        // Extract the updated data from the request
        $updatedData = $request->input('data');

        // Validation rules
        $rules = [
            'assessment' => 'required',
            'old_assessment' => 'required',
            'floor' => 'required',
            'bill_usage' => 'required',
            'aadhar_no' => 'nullable',
            'ration_no' => 'nullable',
            'phone_number' => 'required',
            'owner_name' => 'required',
            'present_owner_name' => 'required',
            'point_gisid' => 'required',
            'old_door_no' => 'required',
            'new_door_no' => 'required',
            'remarks' => 'nullable',
        ];

        // Validate the updated data
        $validator = Validator::make($updatedData, $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Set the updated_at field to the current timestamp
        $updatedData['updated_at'] = Carbon::now();
        unset($updatedData['val']);
        // Ensure created_at is not accidentally overwritten
        unset($updatedData['created_at']);

        // Update the data in the specified table
        try {
            DB::table($tableName)->where('id', $id)->update($updatedData);
            return response()->json(['message' => 'Data updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update data', 'details' => $e->getMessage()], 500);
        }
    }


    public function deleteAssessment(Request $request)
    {
        $id = $request->id;


        $val = (int)$request->input('data.val');
        $data = DB::table('data')->where('id', $val)->first();

        $tableName = $data->pointdata;
        DB::table($tableName)->where('id', $id)->delete();
        return response()->json(['message' => 'Data deleted successfully'], 200);
    }

    public function exportToPdf()
    {
        $data = Data::all();

        $pdf = PDF::loadView('pdf.your_model_export', compact('data'));
        return $pdf->download('data_export.pdf');
    }

    public function replaceGisid(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'dgisid1' => 'required|string',
            'dgisid2' => 'required|string',
        ]);

        $id = $request->input('id');
        $dgisid1 = $request->input('dgisid1');
        $dgisid2 = $request->input('dgisid2');

        $data = Data::findOrFail($id);
        $gisidsExist = DB::table($data->polygon)
            ->whereIn('gisid', [$dgisid1, $dgisid2])
            ->count();

        if ($gisidsExist < 2) {
            return response()->json(['error' => true, 'message' => "Data Not Found"], 404);
        }
        DB::table($data->polygon)
            ->where('gisid', $dgisid1)
            ->update(['gisid' => $dgisid2]);
        DB::table($data->point)
            ->where('gisid', $dgisid1)
            ->update(['gisid' => $dgisid2]);
        $minIdRow = DB::table($data->polygon)
            ->where('gisid', $dgisid2)
            ->orderBy('id', 'asc')
            ->first();

        if ($minIdRow) {
            DB::table($data->polygon)
                ->where('id', $minIdRow->id)
                ->delete();
        }
        $minIdRowp = DB::table($data->point)
            ->where('gisid', $dgisid2)
            ->orderBy('id', 'asc')
            ->first();

        if ($minIdRowp) {
            DB::table($data->point)
                ->where('id', $minIdRowp->id)
                ->delete();
        }
        return response()->json(['success' => true, 'message' => "GIS ID replaced successfully."]);
    }
}
