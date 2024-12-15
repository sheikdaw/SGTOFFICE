<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SurveyorController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'namespace' => 'App\Http\Controllers\Admin', 'as' => 'admin.'], function () {
    // auth
    Route::controller(AuthController::class)->group(function () {
        Route::get('/', 'index')->name("auth.index");
        Route::post('/sign-in', 'sign_in')->name("auth.sign-in");
    });

    Route::middleware(AdminMiddleware::class)->group(function () {
        // dashboard
        Route::group(['prefix' => 'dashboard'], function () {
            Route::controller(DashboardController::class)->group(function () {
                Route::get('/', 'index')->name("dashboard");
            });
        });

        // student
        Route::group(['prefix' => 'student', 'as' => 'student.'], function () {
            Route::controller(StudentController::class)->group(function () {
                Route::get('/', 'index')->name("index");
                Route::post('/', 'store')->name("store");
                Route::get('/{id}', 'edit')->name("edit");
                Route::post('/{id}', 'update')->name("update");

                // ajax
                Route::get('/get-data', 'get_data')->name("get-data");
            });
        });

        // types
        Route::group(['prefix' => 'type', 'as' => 'type.'], function () {
            Route::controller(TypeController::class)->group(function () {
                Route::get('/', 'index')->name("index");
                Route::post('/', 'store')->name("store");

                // ajax
                Route::delete('/{id}', 'distroy')->name("delete");
                Route::get('/get-data', 'get_data')->name("get-data");
            });
        });

        // material
        Route::group(['prefix' => 'material', 'as' => 'material.'], function () {
            Route::controller(MaterialController::class)->group(function () {
                Route::get('/', 'index')->name("index");
                Route::get('/create', 'create')->name("create");
                Route::post('/', 'store')->name("store");
                Route::get('/{id}', 'edit')->name("edit");
                Route::post('/{id}', 'update')->name("update");

                // ajax
                Route::get('/get-data', 'get_data')->name("get-data");
            });
        });

        // Test
        Route::group(['prefix' => 'test', 'as' => 'test.'], function () {
            Route::controller(TestController::class)->group(function () {
                Route::get('/', 'index')->name("index");
                Route::get('/create', 'create')->name("create");
                Route::post('/', 'store')->name("store");
                Route::get('/{id}', 'edit')->name("edit");
                Route::post('/{id}', 'update')->name("update");
            });
        });

        // Video
        Route::group(['prefix' => 'video', 'as' => 'video.'], function () {
            Route::controller(VideoController::class)->group(function () {
                Route::get('/', 'index')->name("index");
                Route::post('/', 'store')->name("store");

                // ajax
                Route::get('/get-data', 'get_data')->name("get-data");
            });
        });

        // auth sign out
        Route::controller(AuthController::class)->group(function () {
            Route::get('/sign-out', 'sign_out')->name("auth.sign-out");
        });
    });
});
