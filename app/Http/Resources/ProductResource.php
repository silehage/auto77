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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price_custom_label,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'is_available' => $this->is_available,
            'category' => $this->category,
            'assets' => $this->assets,
            'images' => $this->images,
        ];
    }
}
