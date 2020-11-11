<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InShoppingCart extends Model
{

  protected $fillable = [
    'products_id',
    'shopping_cart_id'
  ];

}

