<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title', '')),
            'content' => trim((string) $this->input('content', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:7', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:20000'],
            'topic_id' => ['required', 'integer', 'exists:topics,id'],
            'status' => ['sometimes', 'boolean'],
            'image' => $this->is('api/*')
                ? ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9._-]+$/']
                : ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Enter a title.',
            'title.min' => 'The post title must be at least 7 characters.',
            'title.max' => 'The post title must be at most 255 characters.',
            'content.required' => 'Enter the post text.',
            'content.min' => 'The post text must be at least 10 characters.',
            'content.max' => 'The post text must be at most 20000 characters.',
            'topic_id.required' => 'Choose a category.',
            'topic_id.exists' => 'Choose a category.',
            'image.image' => 'The uploaded file is not an image.',
        ];
    }
}
