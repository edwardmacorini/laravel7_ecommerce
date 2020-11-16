<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
  protected $table = 'shopping_cart';

  protected $primaryKey = 'id';

  // Mas assignment
  protected $fillable = ['status'];

  public function approved() {
    $this->updateCustomIDAndStatus();
  }

  public function generateCustomID() {
    return md5("$this->id $this->updated_at");
  }

  public function updateCustomIDAndStatus() {
    $this->status = "approved";
    $this->customid = $this->generateCustomID();
    $this->save(); 
  }

  public function inShoppingCart() {
    return $this->hasMany('App\InShoppingCart');
  }

  public function products() {
    return $this->belongsToMany('App\Products', 'in_shopping_carts');
  }

  public function productsSize() {
    return $this->products()->count();
  }

  public function total() {
    return $this->products()->sum('pricing');
  }

  public function total_USD() {
    return $this->total() / 100;
  }

  //Metodo para buscar una session si no la encuentra la crea
  public static function findOrCreateBySessionID($shopping_cart_id) {

    if($shopping_cart_id)
      // Buscar el carrito de compras con este ID
      return ShoppingCart::findBySession($shopping_cart_id);

    else
      //Crear un carrito de compras
      return ShoppingCart::createWithoutSession();

  }

  public static function findBySession($shopping_cart_id) {

    return ShoppingCart::find($shopping_cart_id);

  }

  public static function createWithoutSession() {

    return ShoppingCart::create([
      'status' => 'incompleted'
    ]);

  }

}
