<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AchievementRequest extends FormRequest
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
            'addAchievement'   =>  $this->getaddAchievementRules(),
            'deleteAchievement'   =>  $this->getdeleteAchievementRules(),
            'add'   =>  $this->getAddRules(),
            'delete'   =>  $this->getdeleteRules(),
            'update'   =>  $this->getupdateRules(),
        };

    }

    public function getaddAchievementRules(){
        return [
            'achievement_id' => 'required|exists:achievements,id',
            'user_id' => 'required|exists:users,id'
        ];
    }

    public function getdeleteAchievementRules(){
        return [
            'achievement_id' => 'required|exists:achievements,id',
            'user_id' => 'required|exists:users,id'
        ];
    }

    public function getAddRules(){
        return [
            "achievement" => "required",
            "points" => "required",
            "description" => "required",
            "segments" => "required"

        ];
    }
    public function getdeleteRules(){
        return [
            'id' => 'required|exists:achievements,id',
        ];
    }
    public function getupdateRules(){
        return [
            "id" => "required",
            "achievement" => "required",
            "points" => "required",
            "description" => "required",
            "segments" => "required"

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
