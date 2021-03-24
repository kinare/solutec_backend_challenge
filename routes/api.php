<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware' => ['json.response']], function () {

    /* Auth Routes */
    Route::group(['namespace' => 'App\Http\Controllers\Authentication','prefix' => 'auth'], function (){
        Route::post('/login', 'AuthController@login');
        Route::post('/register', 'AuthController@register');
        Route::post('/password-reset', 'AuthController@reset');
        Route::post('/password', 'AuthController@password');
        Route::get('/logout', 'AuthController@logout')->middleware('auth:sanctum');
        Route::get('/user', 'AuthController@user')->middleware('auth:sanctum');
    });

    /* Product Routes */
    Route::group(['namespace' => 'App\Http\Controllers','prefix' => 'product', 'middleware' => 'auth:sanctum'], function (){
        Route::get('/', 'ProductController@index');
        Route::get('/{id}', 'ProductController@show');
        Route::post('/{id}', 'ProductController@update');
        Route::post('/', 'ProductController@store');
        Route::delete('/{id}', 'ProductController@destroy');
    });

    /*Supplier Routes*/
    Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => 'supplier', 'middleware' => 'auth:sanctum'], function (){
        Route::get('/', 'SupplierController@index');
        Route::get('/{id}', 'SupplierController@show');
        Route::post('/', 'SupplierController@store');
        Route::post('/{id}', 'SupplierController@update');
        Route::delete('/{id}', 'SupplierController@destroy');
        Route::get('/{id}/products', 'SupplierController@products');
    });

    /*Supplier Products Routes*/
    Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => 'supplier_product', 'middleware' => 'auth:sanctum'], function (){
        Route::get('/', 'SupplierProductController@index');
        Route::get('/{id}', 'SupplierProductController@show');
        Route::post('/', 'SupplierProductController@store');
        Route::post('/{id}', 'SupplierProductController@update');
        Route::delete('/{id}', 'SupplierProductController@destroy');
    });

    /*Order Routes*/
    Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => 'order', 'middleware' => 'auth:sanctum'], function (){
        Route::get('/', 'OrderController@index');
        Route::get('/{id}', 'OrderController@show');
        Route::post('/', 'OrderController@store');
        Route::post('/{id}', 'OrderController@update');
        Route::delete('/{id}', 'OrderController@destroy');
        Route::get('/{id}/products', 'OrderController@details');
    });

    /*Order Details  Routes*/
    Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => 'order_detail', 'middleware' => 'auth:sanctum'], function (){
        Route::get('/', 'OrderDetailController@index');
        Route::get('/{id}', 'OrderDetailController@show');
        Route::post('/', 'OrderDetailController@store');
        Route::post('/{id}', 'OrderDetailController@update');
        Route::delete('/{id}', 'OrderDetailController@destroy');
    });

});

