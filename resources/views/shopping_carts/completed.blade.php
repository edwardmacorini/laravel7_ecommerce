@extends('layouts.app')

@section('content')

<header class="big-padding text-center blue-grey white-text">
  <h1>Compra completada..!</h1>
</header>

<div class="container">
  <div class="card large-padding">
    <h3>Tu pago fue procesado <span class="{{$order->status}}">{{$order->status}}</span></h3>
    <p>Corrobora los detalles de tu envio:</p>

    <div class="row px-4">
      <div class="col-6">Correo</div>
      <div class="col-6">{{$order->email}}</div>
    </div>

    <div class="row px-4">
      <div class="col-6">Direción</div>
      <div class="col-6">{{$order->address()}}</div>
    </div>

    <div class="row px-4">
      <div class="col-6">Codigo Postal</div>
      <div class="col-6">{{$order->postal_code}}</div>
    </div>

    <div class="row px-4">
      <div class="col-6">Ciudad</div>
      <div class="col-6">{{$order->city}}</div>
    </div>


    <div class="row px-4">
      <div class="col-6">Estado y País</div>
      <div class="col-6">{{"$order->state $order->country_code"}}</div>
    </div>

    <div class="text-center mt-5">
      <a href="/{{$shopping_cart->id}}">Link permanente de tu compra</a>
    </div>

  </div>
</div>

@endsection
