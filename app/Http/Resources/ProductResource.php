<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductIndexResource;
use App\Http\Resources\ProductVariationResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends ProductIndexResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return array_merge(parent::toArray($request), [
        //     'variations' => ProductVariationResource::collection(
        //         $this->variations->groupBy('type.name')
        //     )
        // ]);
        // return parent::toArray($request);
                $lang = (request('lang')) ? request('lang') : \App::getLocale();

        return [
            'id'           => $this->id,
            'category'     => $this->category ? $this->category->name : null,
            'name'         => $this->getTranslatedAttribute('name',$lang),
            'description'  => $this->getTranslatedAttribute('description',$lang),
            'image'        => url('storage/'.($this->image)),
            'gallery'      => $this->images(),
            'is_favorite'  => $this->isFavorited(),
            'price'        => $this->price,
            'sale_price'   => $this->sale_price,
            'sale_value'   => $this->price - ($this->price* ($this->sale_price / 100)) .'%',
            'price_list'   => $this->prices,
            'options'      => attributesIndexResource::collection($this->options->unique('attribute_id')),
            'supplier'     => $this->owner ? $this->owner->name: null ,
            'rating'       => 4.5 ,
            'is_used'   => $this->used,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];

    }
    
    
    

}
