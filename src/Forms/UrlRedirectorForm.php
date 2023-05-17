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
                'label_attr' => ['class' => 'control-label required'],
                'attr' => ['placeholder' => 'https://www.example.com'],
            ])
            ->add('target', 'text', [
                'label' => trans('plugins/url-redirector::url-redirector.target'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => ['placeholder' => 'https://www.example.com'],
            ]);
    }
}
