<?php

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

//Route::any("api/{resource}/{id?}/{relation?}/{relatedId?}", "RestController@dispatch")->name('api.dispatch');

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('{namespace}[/{id}[/{relation}[/{relationId}]]]', 'Rest\RestController@handleGet');
    $router->post('{namespace}', 'Rest\RestController@handlePost');
    $router->put('{namespace}/{id}', 'Rest\RestController@handlePut');
    $router->patch('{namespace}/{id}', 'Rest\RestController@handlePut');
    $router->delete('{namespace}/{id}', 'Rest\RestController@handleDelete');
});

