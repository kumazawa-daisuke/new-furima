<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required_without:image|string|max:400',
            'image' => 'nullable|mimes:jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'content.required_without' => '本文を入力してください',
            'content.max' => '本文は400文字以内で入力してください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.max' => '画像サイズは2MB以内でアップロードしてください',
        ];
    }
}
