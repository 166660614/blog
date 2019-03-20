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
$router->post('/user/login','Users\UserController@login');//登录接口
$router->post('/user/center','Users\UserController@center');//用户中心接口
$router->post('/user/order','Users\UserController@order');//防刷
$router->post('/user/api','Users\UserController@api');

$router->post('/h/api','Users\UserController@hapi');

$router->post('/u/ulogin','Users\UserController@ulogin');
