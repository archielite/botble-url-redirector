<?php

namespace ArchiElite\UrlRedirector\Http\Controllers;

use ArchiElite\UrlRedirector\Forms\UrlRedirectorForm;
use ArchiElite\UrlRedirector\Http\Requests\StoreUrlRedirectorRequest;
use ArchiElite\UrlRedirector\Http\Requests\UpdateUrlRedirectorRequest;
use ArchiElite\UrlRedirector\Models\UrlRedirector;
use ArchiElite\UrlRedirector\Tables\UrlRedirectorTable;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;

class UrlRedirectorController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/url-redirector::url-redirector.menu'), route('url-redirector.index'));
    }

    public function index(UrlRedirectorTable $dataTable)
    {
        $this->pageTitle(trans('plugins/url-redirector::url-redirector.menu'));

        return $dataTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/url-redirector::url-redirector.create'));

        return UrlRedirectorForm::create()->renderForm();
    }

    public function store(StoreUrlRedirectorRequest $request)
    {
        $form = UrlRedirectorForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('url-redirector.index'))
            ->setNextUrl(route('url-redirector.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(UrlRedirector $url, FormBuilder $formBuilder)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $url->original]));

        return $formBuilder
            ->create(UrlRedirectorForm::class, ['model' => $url])
            ->setValidatorClass(UpdateUrlRedirectorRequest::class)
            ->renderForm();
    }

    public function update(UrlRedirector $url, UpdateUrlRedirectorRequest $request)
    {
        UrlRedirectorForm::createFromModel($url)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('url-redirector.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(UrlRedirector $url)
    {
        return DeleteResourceAction::make($url);
    }
}
