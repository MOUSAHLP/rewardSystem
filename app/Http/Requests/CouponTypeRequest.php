<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CouponTypeRequest extends FormRequest
{
    use ApiResponser;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            'addcouponsType'   =>  $this->getaddcouponsTypeRules(),
            'updatecouponsType'   =>  $this->getupdatecouponsTypeRules(),
            'deletecouponsType'   =>  $this->getdeletecouponsTypeRules(),
        };

    }

    public function getaddcouponsTypeRules(){
        return [
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,jpg,png',
            'is_percentage' => 'required|boolean',
        ];
    }

    public function getupdatecouponsTypeRules(){
        return [
            'id' =>  'required|integer|exists:coupons_types,id',
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,jpg,png',
            'is_percentage' => 'required|boolean',
        ];
    }

    public function getdeletecouponsTypeRules(){
        return [
            'id' =>  'required|integer|exists:coupons_types,id'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'Error',
            'message' =>$validator->errors()->first(),
            'data' => null,
            'statusCode' => 422

        ], 422));
    }
}
