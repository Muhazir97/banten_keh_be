<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
	
	$router->post('/auth/login', 'AuthController@login');
	$router->post('/test', 'TestController@index');

// ================================= FOR USER ==========================================================
	//KAMUS
	$router->get('kamus/search-keyword', 'KamusController@index');
	//CONTENT
	$router->get('content/branda-content', 'ContentController@branda');
	$router->get('content/search-by-category', 'ContentController@searchByCatagory');
	$router->get('content/detail-content/{id}', 'ContentController@detailContent');
	$router->get('content/counter-visit/{id}', 'ContentController@CounterVisit');
	$router->post('content/upload-image-ck', 'ContentController@uploadImageCk');

// ================================= FOR ADMIN ==========================================================
$router->group(['middleware' => 'jwt.tymon'], function () use ($router){
	// DASHBOARD
	$router->get('dashboard/content-terpopuler', 'DashboardController@ContentTerpopuler');
	$router->get('dashboard/card-kamus', 'DashboardController@CardKamus');
	$router->get('dashboard/card-content', 'DashboardController@CardContent');
	//KAMUS
	$router->get('kamus/index-admin', 'KamusController@IndexAdmin');
	$router->get('kamus/show/{id}', 'KamusController@show');
	$router->post('kamus/create', 'KamusController@store');
	$router->post('kamus/update/{id}', 'KamusController@update');
	$router->post('kamus/delete/{id}', 'KamusController@destroy');
	//CONTENT
	$router->get('content/index-content-admin', 'ContentController@IndexContentAdmin');
	$router->get('content/show/{id}', 'ContentController@show');
	$router->post('content/create', 'ContentController@store');
	$router->post('content/update/{id}', 'ContentController@update');
	$router->post('content/delete/{id}', 'ContentController@destroy');
	//ADMIN
	$router->get('admin/index', 'DataAdminController@index');
	$router->get('admin/show/{id}', 'DataAdminController@show');
	$router->post('admin/create', 'DataAdminController@store');
	$router->post('admin/update/{id}', 'DataAdminController@update');
	$router->post('admin/delete/{id}', 'DataAdminController@destroy');
});
	// $router->get('sap-configuration/sap-sync-master/show/{id}', 'SapSyncMasterController@show');
	// $router->post('sap-configuration/sap-sync-master/update/{id}', 'SapSyncMasterController@update');
	// $router->post('sap-configuration/sap-sync-master/delete/{id}', 'SapSyncMasterController@destroy');
