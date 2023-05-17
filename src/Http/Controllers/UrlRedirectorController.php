<?php

namespace ArchiElite\UrlRedirector\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use ArchiElite\UrlRedirector\Forms\UrlRedirectorForm;
use ArchiElite\UrlRedirector\Http\Requests\StoreUrlRedirectorRequest;
use ArchiElite\UrlRedirector\Http\Requests\UpdateUrlRedirectorRequest;
use ArchiElite\UrlRedirector\Models\UrlRedirector;
use ArchiElite\UrlRedirector\Repositories\Interfaces\UrlRedirectorInterface;
use ArchiElite\UrlRedirector\Tables\UrlRedirectorTable;
use Exception;
use Illuminate\Http\Request;

class UrlRedirectorController extends BaseController
{
    use HasDeleteManyItemsTrait;

    public function __construct(protected UrlRedirectorInterface $urlRedirectorRepository)
    {
    }

    public function index(UrlRedirectorTable $dataTable)
    {
        PageTitle::setTitle(trans('plugins/url-redirector::url-redirector.menu'));

        return $dataTable->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/url-redirector::url-redirector.create'));

        return $formBuilder->create(UrlRedirectorForm::class)->renderForm();
    }

    public function store(StoreUrlRedirectorRequest $request, BaseHttpResponse $response)
    {
        $data = $request->validated();

        $url = $this->urlRedirectorRepository->createOrUpdate($data);

        event(new CreatedContentEvent(URL_REDIRECTOR_MODULE_SCREEN_NAME, $request, $url));

        return $response
            ->setPreviousUrl(route('url-redirector.index'))
            ->setNextUrl(route('url-redirector.edit', $url->id))
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

    public function update(UrlRedirector $url, UpdateUrlRedirectorRequest $request, BaseHttpResponse $response)
    {
        $url->fill($request->input());

        $this->urlRedirectorRepository->createOrUpdate($url);
        event(new UpdatedContentEvent(URL_REDIRECTOR_MODULE_SCREEN_NAME, $request, $url));

        return $response
            ->setPreviousUrl(route('url-redirector.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(UrlRedirector $url, Request $request, BaseHttpResponse $response)
    {
        try {
            $this->urlRedirectorRepository->delete($url);

            event(new DeletedContentEvent(URL_REDIRECTOR_MODULE_SCREEN_NAME, $request, $url));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        return $this->executeDeleteItems($request, $response, $this->urlRedirectorRepository, URL_REDIRECTOR_MODULE_SCREEN_NAME);
    }
}
