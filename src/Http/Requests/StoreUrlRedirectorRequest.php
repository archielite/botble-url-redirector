<?php

namespace ArchiElite\UrlRedirector\Http\Requests;

use Botble\Support\Http\Requests\Request;

class StoreUrlRedirectorRequest extends Request
{
    public function rules(): array
    {
        return [
            'original' => [
                'required',
                'max:255',
                'url',
                'unique:url_redirector',
            ],
            'target' => 'required|max:255|url|different:original',
        ];
    }
}
