<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShoppingCart;
use App\Order;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Exception\PayPalConnectionException;

class PaymentController extends Controller
{
  private $apiContext;

  public function __construct()
  {
    $payPalConfig = config('paypal');

    $this->apiContext = new ApiContext(
      new OAuthTokenCredential(
        $payPalConfig['client_id'],
        $payPalConfig['secret']
      )
    );

    $this->apiContext->setConfig($payPalConfig['settings']);
  }

  // ...

  public function payWithPayPal(Request $request)
  {
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');

    $amount = new Amount();
    $amount->setTotal($request->total_USD);
    $amount->setCurrency('USD');

    $transaction = new Transaction();
    $transaction->setAmount($amount);
    // $transaction->setDescription('See your IQ results');

    $callbackUrl = url('/');

    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl("$callbackUrl/paypal/status")
                 ->setCancelUrl("$callbackUrl/carrito");

    $payment = new Payment();
    $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrls);

    try {
      $payment->create($this->apiContext);
      return redirect()->away($payment->getApprovalLink());
    } catch (PayPalConnectionException $ex) {
      echo $ex->getData();
    }
  }

  public function payPalStatus(Request $request)
  {
    $shopping_cart_id = \Session::get('shopping_cart_id');
    $shopping_cart = ShoppingCart::findOrCreateBySessionID($shopping_cart_id);

    $paymentId = $request->input('paymentId');
    $payerId = $request->input('PayerID');
    $token = $request->input('token');

    if (!$paymentId || !$payerId || !$token) {
      $status = 'Lo sentimos! El pago a través de PayPal no se pudo realizar.';
      return redirect('/paypal/failed')->with(compact('status'));
    }

    $payment = Payment::get($paymentId, $this->apiContext);

    $execution = new PaymentExecution();
    $execution->setPayerId($payerId);

    /** Execute the payment **/
    $result = $payment->execute($execution, $this->apiContext);

    if ($result->getState() === 'approved') {
      \Session::remove('shopping_cart_id');
      $order = Order::createFromPayPalResponse($result, $shopping_cart);
      $shopping_cart->approved();

      return view('shopping_carts.completed', ['shopping_cart' => $shopping_cart, 'order' => $order]);

      //$status = 'Gracias! El pago a través de PayPal se ha ralizado correctamente.';
      //return redirect('/results')->with(compact('status'));
    }

    $status = 'Lo sentimos! El pago a través de PayPal no se pudo realizar.';
    return redirect('/results')->with(compact('status'));
  }

}
