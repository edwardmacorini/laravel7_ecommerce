<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShoppingCart;
use App\Paypal;

class ShoppingCartsController extends Controller
{

  public function index() {

    $shopping_cart_id = \Session::get('shopping_cart_id');

    $shopping_cart = ShoppingCart::findOrCreateBySessionID($shopping_cart_id);


    //    $paypal = new Paypal;
    //
    //    $paypal->generate();
    //
    //    return "";


    $products = $shopping_cart->products()->get();

    $total = $shopping_cart->total();

    return view('shopping_carts.index', ['products' => $products, 'total' => $total]);

  }

}
