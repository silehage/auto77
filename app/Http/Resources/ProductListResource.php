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
            'sku'    => $this->sku,
            'description' =>  $this->description,
            'is_available'  =>  $this->is_available,
            'price' =>  $this->price_custom_label,
            'category' => $this->category,
            'assets'  =>  $this->assets,
            'category_id' => $this->category_id,
          ];
    }
 
}
