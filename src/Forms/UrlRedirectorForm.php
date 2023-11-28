<?php

namespace ArchiElite\UrlRedirector\Forms;

use Botble\Base\Forms\FormAbstract;
use ArchiElite\UrlRedirector\Http\Requests\StoreUrlRedirectorRequest;
use ArchiElite\UrlRedirector\Models\UrlRedirector;

class UrlRedirectorForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this
            ->setupModel(new UrlRedirector())
            ->setValidatorClass(StoreUrlRedirectorRequest::class)
            ->withCustomFields()
            ->add('original', 'text', [
                'label' => trans('plugins/url-redirector::url-redirector.original'),
                'required' => true,
                'attr' => ['placeholder' => 'https://www.example-original.com'],
            ])
            ->add('target', 'text', [
                'label' => trans('plugins/url-redirector::url-redirector.target'),
                'required' => true,
                'attr' => ['placeholder' => 'https://www.example-target.com'],
            ]);
    }
}
