<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:filter',
                'exists:users,email',
                'max:255',
                'string'
            ],
            'password' => [
                'required',
                'regex:/^[!-~]+$/',
                'between:8,255',
                'string',
                function ($attribute, $value, $fail) {
                    $user = User::where('email', $this->email)->first();
                    
                    if (is_null($user) || !Hash::check($value, $user->password)) {
                        $fail(':attributeが一致していません。');
                    }

                    if (!$user->is_enable) {
                        $fail('有効なユーザーではありません。');
                    }
                }
            ],
        ];
    }
    public function attributes(): array
    {
        return [
            'email'    => 'メールアドレス',
            'password' => 'パスワード',
        ];
    }
}
