<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneralController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=>'guest'],function(){
	Route::get('/login',[GeneralController::class,'loginPage'])->name('login');
	Route::post('/login',[GeneralController::class,'doLogin']);
});

Route::group(['middleware'=>'auth'],function(){
	Route::get('/', [GeneralController::class,'admin']);
	Route::post('/master_item',[GeneralController::class,'insertMasterItem']);
	Route::patch('/master_item',[GeneralController::class,'updateMasterItem']);
	Route::get('/list_master_item',[GeneralController::class,'getListMasterItem']);
	Route::get('/detail',[GeneralController::class,'getSingleItem']);
	Route::get('/delete_item',[GeneralController::class,'deleteSingleItem']);

	Route::post('/data_gudang',[GeneralController::class,'dataGudang']);
	Route::get('/logout',[GeneralController::class,'logout']);
});
