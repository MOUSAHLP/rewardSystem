<?php

namespace App\Http\Resources;

use App\Enums\CouponTypes;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'image' => env('APP_URL') . '/storage/images/couponsType/'.$this->image,
            'type'  => CouponTypes::getName($this->type) ,
        ];
    }
    public function IsExpired($expire_at)
    {
        return  $expire_at < Carbon::now();
    }
}
