<?php

use App\Exports\UsersExport;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SurveyorController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/send-test-email', [AdminController::class, 'sendTestEmail']);

Route::middleware('guest:admin,surveyor,cbe,taxcollector')->group(function () {
    // Show login form (GET)
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

    // Handle login form submission (POST)
    Route::post('login', [AuthController::class, 'submitLogin'])->name("submitLogin");

    Route::get('forget', [AuthController::class, 'forgetPassword'])->name("forget-password");
    Route::post('forget-Email', [AuthController::class, 'forgetEmail'])->name("forget-Email");

    Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');
});


Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Admin dashboard route, protected by 'admin' middleware group
Route::middleware('admin')->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/surveyors', [AdminController::class, 'surveyors'])->name('admin.surveyors');
    Route::post('/admin/surveyors', [AdminController::class, 'storeSurveyor'])->name('admin.store-Surveyor');
    Route::post('/admin/Datastore', [AdminController::class, 'dataStore'])->name('admin.datastore');
    Route::get('/admin/cbe', [AdminController::class, 'cbe'])->name('admin.cbe');
    Route::post('/admin/cbestore', [AdminController::class, 'cbeStore'])->name('admin.cbeStore');
    Route::post('/admin/cbeupdate', [AdminController::class, 'cbeUpdate'])->name('admin.cbeUpdate');
    Route::delete('/corporation/{id}', [AdminController::class, 'cbeDestroy'])->name('admin.cbeDelete');
    Route::post('/admin/surveyorupdate', [AdminController::class, 'surveyorUpdate'])->name('admin.surveyorUpdate');
    Route::delete('/surveyor/{id}', [AdminController::class, 'surveyorDestroy'])->name('admin.surveyorDelete');
    Route::get('area/variation', [AdminController::class, 'areaVariation'])->name('admin.area.variation');

    Route::post('variation', [AdminController::class, 'usageVariation'])->name('admin.usage.variation');

    Route::get('and-area/variation/{id}', [AdminController::class, 'usageAndAreaVariation'])->name('admin.usageandarea.variation');

    Route::get('final/format/{id}', [AdminController::class, 'finalFormat'])->name('final.format');
    Route::get('/download-polygons/{id}', [AdminController::class, 'polygonDownload'])->name('admin.downloadPolygons');
    Route::get('/download-points/{id}', [AdminController::class, 'pointDownload'])->name('admin.downloadPoints');
    Route::get('/download-lines/{id}', [AdminController::class, 'roadDownload'])->name('admin.downloadLines');
    Route::get('/download-Street/{id}', [AdminController::class, 'downloadSteetWise'])->name('admin.downloadsteetwise');

    Route::get('surveyor/count/{id}', [AdminController::class, 'surveyorCount'])->name('surveyor.count');
    Route::get('/download-missing-bill/{id}', [AdminController::class, 'downloadMissingBill'])->name('admin.downloadMissingBill');
});
Route::post('/processImage', [AdminController::class, 'processImage'])->name('process.image');


Route::middleware('surveyor')->group(function () {
    Route::get('/surveyor', [SurveyorController::class, 'dashboard'])->name('surveyor.dashboard');
    Route::post('pointdata-upload', [SurveyorController::class, 'uploadPointData'])->name('surveyor.pointdata-upload');
    Route::post('polygondata-upload', [SurveyorController::class, 'uploadPolygonData'])->name('surveyor.polygondata-upload');
    Route::post('addPolygonFeature', [SurveyorController::class, 'addPolygonFeature'])->name('surveyor.addPolygonFeature');
    Route::post('addLineFeature', [SurveyorController::class, 'addLineFeature'])->name('surveyor.addLineFeature');

    Route::post('mergepolygon', [SurveyorController::class, 'mergePolygon'])->name('surveyor.mergePolygon');
    Route::post('deletePolygon', [SurveyorController::class, 'deletePolygon'])->name('surveyor.deletePolygon');
    Route::post('updateRoadName', [SurveyorController::class, 'updateRoadName'])->name('surveyor.updateRoadName');

    Route::get('/surveyor/find-gisid', [SurveyorController::class, 'findGisid'])->name('surveyor.findGisid');

    Route::post('/surveyor/update-assessment', [SurveyorController::class, 'updateAssessment'])->name('surveyor.updateAssessment');
});

// CBE dashboard route, protected by 'cbe' middleware group
Route::middleware('cbe')->group(function () {
    Route::get('/cbe', function () {
        return view('cbe.dashboard');
    })->name('cbe.dashboard');
});

// Tax Collector dashboard route, protected by 'taxcollector' middleware group
Route::middleware('taxcollector')->group(function () {
    Route::get('/taxcollector', function () {
        return view('taxcollector.dashboard');
    })->name('taxcollector.dashboard');
});
