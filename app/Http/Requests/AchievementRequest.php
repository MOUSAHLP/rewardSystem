<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AchievementRequest extends FormRequest
{
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
            'update'   =>  $this->getUpdateRules(),
        };

    }

    public function getaddAchievementRules(){
        return [
            'achievement_id' => 'required',
            'user_id' => 'required'
        ];
    }

}
