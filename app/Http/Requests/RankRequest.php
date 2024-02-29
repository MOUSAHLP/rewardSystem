<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class RankRequest extends FormRequest
{

    use ApiResponser;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            'addRank'   =>  $this->getaddRankRules(),
            'updateRank'   =>  $this->getupdateRankRules(),
            'deleteRank'   =>  $this->getdeleteRankRules(),
        };

    }

    public function getaddRankRules(){
        return [
            "name" => "required|string",
            "limit" => "required|integer|unique:user_ranks,limit",
            "features.coupon_per_mounth" => "required",
            "features.discount_on_deliver" => "required",
            "description" => "required|string",
            'color' => [
                'required',
                'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'
            ]
        ];
    }

    public function getupdateRankRules(){
        return [
            "id" => "required|integer|exists:user_ranks,id",
            "name" => "required|string",
            "limit" => "required|integer|unique:user_ranks,limit,".$this->id,
            "features.coupon_per_mounth" => "required",
            "features.discount_on_deliver" => "required",
            "description" => "required|string",
            'color' => [
                'required',
                'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'
            ]
        ];
    }
    public function getdeleteRankRules(){
        return [
            "id" => "required|integer|exists:user_ranks,id",
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
