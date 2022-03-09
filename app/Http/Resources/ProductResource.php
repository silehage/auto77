<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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

        $pricing = [
            'default_price' => $this->price,
            'current_price' => $this->price,
            'discount_value' => 0,
            'is_discount' => false 
        ];

        $disc = null;
 
        if($this->discount_id && $this->discount) {
            $disc = $this->discount;
        } elseif($this->promote_id && $this->promote) {
            $disc = $this->promote->discount;
        }

        if($disc) {

            $pricing['is_discount'] = true;

            $discValue = 0;
            

            if($disc->unit == 'percent') {
 
                $discValue = ($this->price*$disc->value) / 100;

                $pricing['current_price'] = $this->price - ($this->price*$disc->value / 100);
                $pricing['discount_percent'] = $disc->value;
                
             } else{
 
                 $discValue = $disc->value;
                 $pricing['current_price'] = $this->price - (int) $disc->value;
                 $pricing['discount_percent'] = number_format(((int)$disc->value / $this->price)*100, 1);
 
            }

            $pricing['discount_value'] = $discValue;
         }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'pricing' => $pricing,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'stock' => $this->variant_items_sum_item_stock?? $this->stock,
            'description' => $this->description,
            'status' => $this->status,
            'sold' => $this->sold,
            'rating' => $this->reviews_avg_rating ? number_format($this->reviews_avg_rating, 1) : 0,
            'weight' => $this->weight,
            'category' => $this->category,
            'assets' => $this->assets,
            'reviews' => $this->reviews,
            'reviews_count' => $this->reviews_count,
            'variants' => $this->variants,
            'discount' => $disc
        ];
    }
}
