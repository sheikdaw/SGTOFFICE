<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

use Livewire\Livewire;

class SurveyorController extends Controller
{
    public function dashboard()
    {
        $surveyor = Auth::guard('surveyor')->user();

        if (!$surveyor) {
            return redirect()->route('login')->withErrors(['email' => 'You need to log in first.']);
        }

        $data = Data::find($surveyor->data_id);
        // return response()->json($data);
        if (!$data) {
            return redirect()->route('login')->withErrors(['email' => 'Data not found.']);
        }

        $polygons = DB::table($data->polygon)->get();
        $points = DB::table($data->point)->get();
        $lines = DB::table($data->line)->get();
        $polygonDatas = DB::table($data->polygondata)->get();
        $pointDatas = DB::table($data->pointdata)->get();
        $mis = DB::table($data->mis)->get();
        $image = $data->image;

        // Get extensions data
        $extensions = [
            'left' => $data->extend_left,
            'right' => $data->extend_right,
            'top' => $data->extend_top,
            'bottom' => $data->extend_bottom,
        ];

        $username = $surveyor->name;

        // Filter pointDatas where worker_name equals the authenticated username
        $filteredPointDatas = $pointDatas->filter(function ($point) use ($username) {
            return $point->worker_name === $username;
        });

        // Count the filtered pointDatas
        $pointCount = $filteredPointDatas->count();
        $username = $surveyor->name;
        $filteredPointDatas = $pointDatas->filter(function ($point) use ($username) {
            return $point->worker_name === $username;
        });

        // Count the filtered pointDatas
        $pointCount = $filteredPointDatas->count();
        return view('surveyor.dashboard', compact(
            'data',
            'polygons',
            'points',
            'lines',
            'polygonDatas',
            'pointDatas',
            'mis',
            'extensions',
            'image',
            'pointCount'
        ));
    }
    public function uploadPolygonData(Request $request)
    {
        $surveyor = auth()->guard('surveyor')->user();

        if (!$surveyor) {
            return response()->json(['error' => 'Surveyor not authenticated.'], 401);
        }

        $data = Data::find($surveyor->data_id);

        if (!$data) {
            return response()->json(['error' => 'Data not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'gisid' => 'required',
            'number_bill' => 'required',
            'number_shop' => 'required',
            'number_floor' => 'required',
            'liftroom' => 'required',
            'headroom' => 'required',
            'overhead_tank' => 'required',
            'percentage' => 'required',
            'new_address' => 'required',
            'road_name' => 'required',
            'building_type' => 'required',
            'basement' => 'numeric|required',
            'building_name' => 'required',
            'building_usage' => 'required',
            'construction_type' => 'required',
            'ugd' => 'required',
            'rainwater_harvesting' => 'required',
            'parking' => 'required',
            'ramp' => 'required',
            'cctv' => 'required',
            'hoarding' => 'required',
            'cell_tower' => 'required',
            'solar_panel' => 'required',
            'water_connection' => 'required',
            'phone' => 'required',
            'remarks' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif' // Max 2MB image file size
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'msg' => 'Please fill in all required fields correctly.' // Flash message text
            ], 422);
        }

        $validatedData = $validator->validated();
        $polygonDataTable = DB::table($data->polygondata);
        $polygonData = $polygonDataTable->where('gisid', $validatedData['gisid'])->first();

        $dataToSave = [
            'data_id' => $data->id,
            'number_bill' => $validatedData['number_bill'],
            'number_shop' => $validatedData['number_shop'],
            'number_floor' => $validatedData['number_floor'],
            'liftroom' => $validatedData['liftroom'],
            'headroom' => $validatedData['headroom'],
            'overhead_tank' => $validatedData['overhead_tank'],
            'percentage' => $validatedData['percentage'],
            'new_address' => $validatedData['new_address'],
            'building_name' => $validatedData['building_name'],
            'building_usage' => $validatedData['building_usage'],
            'construction_type' => $validatedData['construction_type'],
            'road_name' => $validatedData['road_name'],
            'ugd' => $validatedData['ugd'],
            'rainwater_harvesting' => $validatedData['rainwater_harvesting'],
            'parking' => $validatedData['parking'],
            'ramp' => $validatedData['ramp'],
            'hoarding' => $validatedData['hoarding'],
            'cell_tower' => $validatedData['cell_tower'],
            'solar_panel' => $validatedData['solar_panel'],
            'water_connection' => $validatedData['water_connection'],
            'phone' => $validatedData['phone'],
            'cctv' => $validatedData['cctv'],
            'basement' => $validatedData['basement'],
            'building_type' => $validatedData['building_type'],
            'worker_name' => $surveyor->name,
            'remarks' => $validatedData['remarks'],
            'created_at' => now(),
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = public_path('/corporation/' . $data->corporation_name . '/' . $data->zone . '/' . $data->ward . '/images/');
            $imageName = $validatedData['gisid'] . '.' . $image->getClientOriginalExtension();

            // Delete the existing image if it exists
            if ($polygonData && $polygonData->image && file_exists($imagePath . $polygonData->image)) {
                unlink($imagePath . $polygonData->image);
            }

            // Create the directory if it doesn't exist
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            // Move the uploaded image to the target directory
            $image->move($imagePath, $imageName);

            // Save the relative path to the database
            $dataToSave['image'] = '/corporation/' . $data->corporation_name . '/' . $data->zone . '/' . $data->ward . '/images/' . $imageName;
        }


        if ($polygonData) {
            $polygonDataTable->where('gisid', $validatedData['gisid'])->update($dataToSave);
            $message = 'Polygon data updated successfully.';
        } else {
            $polygonDataTable->insert(array_merge($dataToSave, ['gisid' => $validatedData['gisid'], 'sqfeet' => $request->input('sqfeet')]));
            $message = 'Polygon data saved successfully.';
        }

        $polygons = DB::table($data->polygon)->get();
        $polygonDatas = DB::table($data->polygondata)->get();

        return response()->json([
            'success' => true,
            'message' => $message,
            'polygon' => $polygons,
            'polygonDatas' => $polygonDatas
        ]);
    }

    public function uploadPointData(Request $request)
    {
        $surveyor = auth()->guard('surveyor')->user();

        if (!$surveyor) {
            return response()->json(['error' => 'Surveyor not authenticated.'], 401);
        }

        $data = Data::find($surveyor->data_id);
        if (!$data) {
            return response()->json(['error' => 'Data not found.'], 404);
        }

        $rules = [
            'point_gisid' => 'required',
            'eb' => 'required',
            'old_assessment' => 'required',
            'floor' => 'required',
            'bill_usage' => 'required',
            'phone_number' => 'required',
            'owner_name' => 'required',
            'present_owner_name' => 'required',
            'old_door_no' => 'required',
            'new_door_no' => 'nullable',
            'water_tax' => 'nullable',
            'remarks' => 'nullable',
        ];

        if ($request->type === "OLD") {
            $rules['assessment'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'msg' => 'Validation errors occurred.'], 422);
        }
        if ($request->type === "OLD") {
            $misDataExists = DB::table($data->mis)->where("assessment", $request->assessment)->exists();
            if (!$misDataExists) {
                return response()->json(['msg' => 'Assessment not found in MIS table.'], 422);
            }
        }

        $validatedData = $validator->validated();


        $polygonData = DB::table($data->polygondata)
            ->where('gisid', $validatedData['point_gisid'])
            ->first();

        if (!$polygonData) {
            return response()->json(['msg' => 'Enter building data first.'], 422);
        }
        if (($polygonData->building_usage !=  $validatedData['bill_usage']) ||  $polygonData->building_usage != 'Mixed') {
        }
        $pointGisidCount = DB::table($data->pointdata)
            ->where('point_gisid', $validatedData['point_gisid'])
            ->count();

        if ($pointGisidCount >= $polygonData->number_bill) {
            return response()->json(['msg' => 'Bill limit for this building has been reached.'], 422);
        }

        DB::table($data->pointdata)->insert([
            'point_gisid' => $validatedData['point_gisid'],
            'worker_name' => $surveyor->name,
            'eb' => $validatedData['eb'],
            'old_assessment' => $validatedData['old_assessment'],
            'floor' => $validatedData['floor'],
            'bill_usage' => $validatedData['bill_usage'],
            'phone_number' => $validatedData['phone_number'],
            'owner_name' => $validatedData['owner_name'],
            'present_owner_name' => $validatedData['present_owner_name'],
            'old_door_no' => $validatedData['old_door_no'],
            'new_door_no' => $validatedData['new_door_no'],
            'water_tax' => $validatedData['water_tax'],
            'remarks' => $validatedData['remarks'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $pointDatas = DB::table($data->pointdata)->get();
        $points = DB::table($data->point)->get();
        $username = $surveyor->name;
        $filteredPointDatas = $pointDatas->filter(function ($point) use ($username) {
            return $point->worker_name === $username;
        });

        // Count the filtered pointDatas
        $pointCount = $filteredPointDatas->count();
        return response()->json(['message' => 'Data uploaded successfully.', 'pointDatas' => $pointDatas, 'points' => $points, 'pointCount' => $pointCount], 200);
    }
    public function addPolygonFeature(Request $request)
    {
        $surveyor = auth()->guard('surveyor')->user();
        if (!$surveyor) {
            return response()->json(['error' => 'Surveyor not authenticated.'], 401);
        }
        // Fetch the data record
        $data = Data::find($surveyor->data_id);
        if (!$data) {
            return response()->json(['error' => 'Data not found.'], 404);
        }
        $polygons = DB::table($data->polygon)->get();
        $maxGisId = $polygons->max('gisid');
        $newGisId = $maxGisId ? $maxGisId + 1 : 1;
        $insidePoint = $this->calculateDataMidpoint($request->coordinates);
        $polygonData = [
            'gisid' => $newGisId,
            'coordinates' => json_encode($request->coordinates), // Store coordinates as JSON
            'type' => 'Polygon',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table($data->polygon)->insert($polygonData);
        $pointData = [
            'gisid' => $newGisId,
            'coordinates' => json_encode($insidePoint), // Store point as JSON
            'type' => 'Point',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table($data->point)->insert($pointData);

        $polygons = DB::table($data->polygon)->get();
        $points = DB::table($data->point)->get();

        return response()->json([
            'message' => 'Feature added successfully.',
            'points' => $points,

            'polygons' => $polygons
        ], 200);
    }
    private function calculateDataMidpoint($coordinates)
    {
        $totalPoints = count($coordinates[0]);
        $midpoint = [0, 0];

        foreach ($coordinates[0] as $point) {
            $midpoint[0] += $point[0];
            $midpoint[1] += $point[1];
        }

        $midpoint[0] /= $totalPoints;
        $midpoint[1] /= $totalPoints;

        return $midpoint;
    }
    public function mergePolygon(Request $request)
    {
        // return response()->json("hi", 200);
        $surveyor = auth()->guard('surveyor')->user();
        if (!$surveyor) {
            return response()->json(['error' => 'Surveyor not authenticated.'], 401);
        }
        $data = Data::find($surveyor->data_id);
        if (!$data) {
            return response()->json(['error' => 'Data not found.'], 404);
        }
        $polygonFirst = DB::table($data->polygon)->where('gisid', $request->firstmerge)->first();
        $pointDatas = DB::table($data->pointdata)->where('point_gisid', $request->firstmerge)->get();
        $polygonSecond = DB::table($data->polygon)->where('gisid', $request->secondmerge)->first();

        if (!$polygonFirst || !$polygonSecond) {
            return response()->json(['error' => 'One or both polygons not found.'], 404);
        }
        $coordinates = array_merge(json_decode($polygonFirst->coordinates, true), json_decode($polygonSecond->coordinates, true));

        $polygonDataFirst = DB::table($data->polygondata)->where('gisid', $request->firstmerge)->first();

        DB::table($data->polygon)
            ->where('gisid', $request->firstmerge)
            ->update(['coordinates' => json_encode($coordinates)]);
        DB::table($data->pointdata)
            ->where('point_gisid', $request->secondmerge)
            ->update(['point_gisid' => $request->secondmerge]);
        DB::table($data->point)->where('gisid', $request->secondmerge)->delete();
        DB::table($data->polygon)->where('gisid', $request->secondmerge)->delete();
        $polygons = DB::table($data->polygon)->get();
        $points = DB::table($data->point)->get();
        return response()->json([
            'message' => 'Feature Merge successfully.',
            'points' => $points,
            'polygons' => $polygons
        ], 200);
    }
    public function deletePolygon(Request $request)
    {
        $surveyor = auth()->guard('surveyor')->user();

        if (!$surveyor) {
            return response()->json(['error' => 'Surveyor not authenticated.'], 401);
        }

        $data = Data::find($surveyor->data_id);

        if (!$data) {
            return response()->json(['error' => 'Data not found.'], 404);
        }

        $pointdata = DB::table($data->pointdata)->where('point_gisid', $request->gisid)->first();
        if ($pointdata) {
            return response()->json([
                'error' => 'Data found. Please contact the team.',
                'name' => $pointdata->worker_name ?? 'N/A' // Ensures safe access if worker_name is missing
            ], 403); // Use 403 for forbidden action
        }

        // Perform deletion
        DB::table($data->point)->where('gisid', $request->gisid)->delete();
        DB::table($data->polygon)->where('gisid', $request->gisid)->delete();
        DB::table($data->polygondata)->where('gisid', $request->gisid)->delete();


        // Fetch updated lists
        $polygons = DB::table($data->polygon)->get();
        $points = DB::table($data->point)->get();

        return response()->json([
            'message' => 'Feature deleted successfully.',
            'points' => $points,
            'polygons' => $polygons
        ], 200);
    }
    public function updateRoadName(Request $request)
    {
        $surveyor = auth()->guard('surveyor')->user();

        if (!$surveyor) {
            return response()->json(['error' => 'Surveyor not authenticated.'], 401);
        }

        $data = Data::find($surveyor->data_id);

        if (!$data) {
            return response()->json(['error' => 'Data not found.'], 404);
        }

        $validatedData = $request->validate([
            'linegisid' => 'required',
            'roadname' => 'required|string|max:255',
        ]);


        $update = DB::table($data->line)
            ->where('gisid', $request->linegisid)
            ->update(['road_name' => $request->roadname]);


        if ($update) {
            $line = DB::table($data->line)->get();
            return response()->json(['message' => 'Data submitted successfully!', 'line' => $line]);
        } else {
            return response()->json(['error' => 'Update failed.'], 500);
        }
    }
}
