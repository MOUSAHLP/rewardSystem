<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    use ApiResponser;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            'addCoupon'   =>  $this->getaddCouponRules(),
            'updateCoupon'   =>  $this->getupdateCouponRules(),
            'deleteCoupon'   =>  $this->getdeleteCouponRules(),
            'buyCoupon'   =>  $this->getbuyCouponRules(),
        };
    }

    public function getaddCouponRules()
    {
        return [
            "coupon_type_id"=>"required|exists:coupons_types,id",
            "value"=> "required|integer",
            "description" => "required|max:1000",
        ];
    }

    public function getupdateCouponRules(){
        return [
            "id"=>"required|exists:coupons,id",
            "coupon_type_id"=>"required|exists:coupons_types,id",
            "value"=> "required|integer",
            "coupon_code"=> [
                'required',
                'unique:coupons,coupon_code,'. $this->id
            ],
            "description" => "required|max:1000",
        ];
    }

    public function getdeleteCouponRules()
    {
        return [
            "id" => "required|integer|exists:coupons,id"
        ];
    }

    public function getbuyCouponRules()
    {
        return [
            "user_id" => "required|integer|exists:users,id",
            "coupon_id" => "required|integer|exists:coupons,id"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'Error',
            'message' => $validator->errors()->first(),
            'data' => null,
            'statusCode' => 422

        ], 422));
    }
}
