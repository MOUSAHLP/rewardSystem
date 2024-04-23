<?php

namespace App\Http\Resources;

use App\Enums\OrderPriorities;
use App\Enums\RanksFeatures;
use App\Models\Rank;
use Illuminate\Http\Resources\Json\JsonResource;

class RankResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id ?? 0,
            'name'        => $this->name ?? "",
            'limit'       => isset($this->limit) ? (int)$this->limit : 0,
            'features'    => isset($this->limit) ? $this->featuresDescription($this->features) : [["name" => "", "value" => 0, "description"=>""]],
            'description' => $this->description ?? "",
            'color'       => $this->color ?? "#ffffff",
        ];
    }
    public function featuresDescription($features)
    {
        $New_features = [];
        foreach (array_keys($features) as $feature) {
            $New_features[] = [
                "name" => $feature,
                "value" => $features[$feature],
                "description" => $this->getfeatureDescription($features, $feature),
            ];
        }
        return $New_features;
    }
    public function getfeatureDescription($features, $feature)
    {
        if ($feature == RanksFeatures::COUPON_PER_MONTH) {
            $text = "كوبون حسم شهرياً عدد ";
            return $text . $features[$feature];
        }
        if ($feature == RanksFeatures::DISCOUNT_ON_DELIVER) {
            return   "حسم " . $features[$feature] . "% على التوصيل";
        }
        if ($feature == RanksFeatures::DELIVER_PRIORITY) {
            return OrderPriorities::getNameArabic($features[$feature]) . " في التوصيل";
        }

        return $feature;
    }
}
