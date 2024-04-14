<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class PointRequest extends FormRequest
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
            'addPointsToUser'   =>  $this->getaddPointsToUserRules(),
            'setPointsValue'   =>  $this->getsetPointsValueRules(),
        };
    }
    public function getaddPointsToUserRules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'points' => 'required|numeric',
            'achievement_id' => 'required|exists:achievements,id',
        ];
    }

    public function getsetPointsValueRules()
    {
        return [
            'value' => 'required|numeric',
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
