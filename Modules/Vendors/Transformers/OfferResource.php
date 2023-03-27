<?php

namespace Modules\Vendors\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request){
        // return parent::toArray($request);
        return [
            'name' => $this->getTranslation('name', \App::getLocale()),
            'description' => $this->getTranslation('description', \App::getLocale()),
            'amount' => $this->amount,
            'value' => $this->value,
            'starting_data' => $this->starting_data,
            'ended_data' => $this->ended_data,
            'vendor' =>[
                'id' => $this->vendor_id,
                'id' => $this->vendor->company_name,
            ],
            'image_url' => $this->image_url
        ];
    }
}
