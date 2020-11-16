@extends('layouts.app')
@section('content')

<div class="big-padding text-center blue-grey white-text">
  <h1>Tu carrito de compras</h1>
</div>

<div class="containter">
  <table class="table table-bordered">
    <thead>
      <tr>
        <td>Producto</td>
        <td>Precio</td>
      </tr>
    </thead>
    <tbody>
      @foreach($products as $product)
        <tr>
          <td>{{ $product->title }}</td>
          <td>{{ $product->pricing }}</td>
        </tr>
      @endforeach

      <tr>
        <td>Total</td>
        <td>{{$total}}</td>
      </tr>
    </tbody>
  </table>
  <div class="text-center">
    {!! Form::open(['url' => '/paypal/pay', 'method' => 'post']) !!}
      <input type="hidden" name="total_USD" value="{{$total_USD}}">
      <input type="submit" value="Paypal" class="btn btn-info active">
    {!! Form::close() !!}
  </div>
</div>

@endsection
