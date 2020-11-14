<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
  protected $table = "products";

  protected $primaryKey = "id";

  protected $fillable = [
    'title',
    'description',
    'pricing',
    'user_id'
  ];

}
