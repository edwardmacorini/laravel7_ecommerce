<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'MainController@home');

Route::get('/carrito', 'ShoppingCartsController@index');

Route::post('/paypal/pay', 'PaymentController@payWithPaypal');

Route::get('/paypal/status', 'PaymentController@payPalStatus');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('products', 'ProductsController');

Route::resource('in_shopping_carts', 'InShoppingCartsController')->only([
  'store','destroy'
]);
