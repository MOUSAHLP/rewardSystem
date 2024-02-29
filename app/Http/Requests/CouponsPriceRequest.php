<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
class CouponsPriceRequest extends FormRequest
{
    use ApiResponser;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            'addCouponsPrice'   =>  $this->getaddCouponsPriceRules(),
            'updateCouponsPrice'   =>  $this->getupdateCouponsPriceRules(),
            'deleteCouponsPrice'   =>  $this->getdeleteCouponsPriceRules(),
        };
    }

    public function getaddCouponsPriceRules()
    {
        return [
            'coupon_id' => 'required|exists:coupons,id',
            'coupon_price' => 'required|integer',
        ];
    }
    public function getupdateCouponsPriceRules()
    {
        return [
            "id" => "required|integer|exists:coupon_prices,id",
            'coupon_id' => 'required|exists:coupons,id',
            'coupon_price' => 'required|integer',
        ];
    }
    public function getdeleteCouponsPriceRules()
    {
        return [
            "id" => "required|integer|exists:coupon_prices,id"
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
