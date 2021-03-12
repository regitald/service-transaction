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

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->get('/shipping', 'API\GeneralController@shipping');
    $router->get('/payment', 'API\GeneralController@payment');

    //endpoint cart bisa dipakai juga untuk mengambil data history dengan melempar type is_history true
    $router->get('/cart', 'API\OrderController@index');

    $router->post('/cart', 'API\OrderController@store');
    $router->put('/cart/update', 'API\OrderController@updateQty');
    $router->delete('/cart/delete', 'API\OrderController@delete');
    
    //checkout response akan menampikan detail receipt
    $router->post('/checkout', 'API\OrderController@checkout');
});
