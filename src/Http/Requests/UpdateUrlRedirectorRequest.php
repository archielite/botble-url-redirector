<?php

namespace ArchiElite\UrlRedirector\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateUrlRedirectorRequest extends Request
{
    public function rules(): array
    {
        return [
            'original' => [
                'required',
                'max:255',
                'url',
                Rule::unique('url_redirector')->ignoreModel($this->route('url')),
            ],
            'target' => 'required|max:255|url|different:original',
        ];
    }
}
