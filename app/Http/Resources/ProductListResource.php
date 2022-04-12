<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */


    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id'      => $this->id,
            'title'   => $this->title,
            'slug'    => $this->slug,
            'sku'     => $this->sku,
            'description' =>  $this->description,
            'status'  =>  $this->status,
            'sold'    =>  $this->sold,
            'weight'  =>  $this->weight,
            'rating'  =>  $this->reviews_avg_rating ? (float) number_format($this->reviews_avg_rating, 1) : 0,
            'pricing' =>  $this->setPricing($this),
            'category' => $this->category,
            'assets'  =>  $this->assets,
            'kampret' => 'kampret'
          ];
    }

    protected function setPricing($product)
    {
      $pricing = [
        'default_price' => $product->price,
        'current_price' => $product->price,
        'discount_value' => 0,
        'discount_percent' => 0,
        'is_discount' => false ,
      ];
      
      $disc = null;
      
      if($product->discount_id != null) {
          $disc = $product->discount;
      } elseif($product->promote_id != null && $product->promote) {
          $disc = $product->promote->discount;
      }
  
      
      if($disc) {
      
          $pricing['is_discount'] = true;
      
          $discValue = 0;
          
      
          if($disc->unit == 'percent') {
      
              $discValue = ($product->price*$disc->value) / 100;
      
              $pricing['current_price'] = $product->price - ($product->price*$disc->value / 100);
              $pricing['discount_percent'] = $disc->value;
              
          } else{
      
              $discValue = $disc->value;
              $pricing['current_price'] = $product->price - (int) $disc->value;
              $pricing['discount_percent'] = number_format(((int)$disc->value / $product->price)*100, 1);
      
          }
      
          $pricing['discount_value'] = $discValue;
      }
      
        return $pricing;
    }
}
