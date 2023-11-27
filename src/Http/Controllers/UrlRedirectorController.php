<?php

namespace ArchiElite\UrlRedirector\Http\Controllers;

use ArchiElite\UrlRedirector\Tables\UrlRedirectorTable;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use ArchiElite\UrlRedirector\Forms\UrlRedirectorForm;
use ArchiElite\UrlRedirector\Http\Requests\UpdateUrlRedirectorRequest;
use ArchiElite\UrlRedirector\Models\UrlRedirector;
use ArchiElite\UrlRedirector\Repositories\Interfaces\UrlRedirectorInterface;
use Illuminate\Http\Request;

class UrlRedirectorController extends BaseController
{
    use HasDeleteManyItemsTrait;

    public function __construct(protected UrlRedirectorInterface $urlRedirectorRepository)
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/url-redirector::url-redirector.menu'),  route('url-redirector.index'));
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

    public function store()
    {
        $form = UrlRedirectorForm::create();
        $form->save();

        return  $this
            ->httpResponse()
            ->setPreviousUrl(route('url-redirector.index'))
            ->setNextUrl(route('url-redirector.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(UrlRedirector $url, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $url->original]));

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

    public function deletes(Request $request)
    {
        return $this->executeDeleteItems($request, $this->httpResponse(), $this->urlRedirectorRepository, URL_REDIRECTOR_MODULE_SCREEN_NAME);
    }
}
