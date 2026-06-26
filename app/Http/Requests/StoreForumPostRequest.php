<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreForumPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->isElu() || $user->isAdmin());
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
            'reply_to_post_id' => ['nullable', 'integer', Rule::exists('forum_posts', 'id')],
        ];
    }
}
