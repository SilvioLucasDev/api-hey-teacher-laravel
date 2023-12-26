<?php

namespace App\Http\Requests\Question;

use App\Rules\WithQuestionMarkRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read string $question
 */
class UpdateQuestion extends FormRequest
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
        return [
            'question' => [
                'required',
                new WithQuestionMarkRule(),
                'min:10',
                Rule::unique('questions')->ignore($this->route()->question->id),
            ],
        ];
    }
}