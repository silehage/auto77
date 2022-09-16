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

        $defaultPrice = $this->price;

        $pricing = [
            'default_price' => $defaultPrice,
            'current_price' => $defaultPrice,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'is_discount' => false,
        ];

        $disc = null;
 
        if($this->productPromo) {
            $disc = $this->productPromo;
        } 

        if($disc) {

            $pricing['is_discount'] = true;

            $discountVal = 0;
            

            if($disc->discount_type == 'PERCENT') {
 
                $discountVal = ($defaultPrice*$disc->discount_amount) / 100;

                $pricing['current_price'] = $defaultPrice - (int) $discountVal;
                $pricing['discount_percent'] = (int) $disc->discount_amount;
                
             } else{
 
                 $discountVal = $disc->discount_amount;

                 $pricing['current_price'] = $defaultPrice - (int) $discountVal;

                 $pricing['discount_percent'] = number_format(((int) $disc->discount_amount / $defaultPrice)*100, 0);
 
            }

            $pricing['discount_amount'] = $discountVal;
         }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'pricing' => $pricing,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'is_available' => $this->is_available,
            'category' => $this->category,
            'assets' => $this->assets,
            'varians' => $this->varians,
            'discount' => $disc,
            'images' => $this->images,
        ];
    }
}
