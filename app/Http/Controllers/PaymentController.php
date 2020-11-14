<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShoppingCart;
use Illuminate\Support\Facades\Config;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;

class PaymentController extends Controller
{
  private $apiContext;

  public function __construct()
  {
    $payPalConfig = Config::get('paypal');

    $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
               $payPalConfig['client_id'], 
               $payPalConfig['secret']
            )
    );
  }

  public function payWithPaypal() {

    $shopping_cart_id = \Session::get('shopping_cart_id');
    $shopping_cart = ShoppingCart::findOrCreateBySessionID($shopping_cart_id);
    $products = $shopping_cart->products()->get();
    $total_USD = $shopping_cart->total_USD();

    $payer = new Payer();
    $payer->setPaymentMethod('paypal');
    
    $amount = new Amount();
    $amount->setTotal($total_USD);
    $amount->setCurrency('USD');

    $items = [];
    foreach($products as $product) {
      $item = new Item();
      $item->setName($product->title)
           ->setDescription($product->description);
      $items[] = $item;
    }

    $transaction = new Transaction();
    $transaction->setAmount($amount);
    $transaction->setItemList($items);
    $transaction->setDescription('Paying make by ecommerce-php');

    $baseURL = url('url');
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl("$baseURL/paypal/status")
        ->setCancelUrl("$baseURL/carrito");
    
    $payment = new Payment();
    $payment->setIntent('sale')
        ->setPayer($payer)
        ->setTransactions(array($transaction))
        ->setRedirectUrls($redirectUrls);

    try {
        $payment->create($this->apiContext);
        //echo $payment;
         
        return redirect()->away($payment->getApprovalLink());
    } catch (\PayPal\Exception\PayPalConnectionException $ex) {
        echo $ex->getData();
    }

  } // payWithPaypal


  public function payPalStatus() {
    
  }

}
