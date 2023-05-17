<?php

namespace ArchiElite\UrlRedirector\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Supports\Builder;
use ArchiElite\UrlRedirector\Models\UrlRedirector;
use ArchiElite\UrlRedirector\Repositories\Interfaces\UrlRedirectorInterface;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class UrlRedirectorTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, UrlRedirectorInterface $urlRedirectorRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $urlRedirectorRepository;

        if (! Auth::user()->hasAnyPermission(['url-redirector.edit', 'url-redirector.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('original', function (UrlRedirector $item) {
                if (! Auth::user()->hasPermission('url-redirector.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('url-redirector.edit', $item->id), BaseHelper::clean($item->original));
            })
            ->editColumn('checkbox', function (UrlRedirector $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function (UrlRedirector $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function (UrlRedirector $item) {
                return $this->getOperations('url-redirector.edit', 'url-redirector.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'original',
            'target',
            'visits',
        ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'original' => [
                'title' => trans('plugins/url-redirector::url-redirector.original'),
                'width' => 'text-start',
            ],
            'target' => [
                'title' => trans('plugins/url-redirector::url-redirector.target'),
                'width' => 'text-start',
            ],
            'visits' => [
                'title' => trans('plugins/url-redirector::url-redirector.visits'),
                'width' => 'text-start',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('url-redirector.create'), 'url-redirector.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('url-redirector.deletes'), 'url-redirector.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'original' => [
                'title' => trans('plugins/url-redirector::url-redirector.original'),
                'type' => 'text',
                'validate' => 'required|max:255',
            ],
            'target' => [
                'title' => trans('plugins/url-redirector::url-redirector.target'),
                'type' => 'text',
                'validate' => 'required|max:255',
            ],
            'visits' => [
                'title' => trans('plugins/url-redirector::url-redirector.visits'),
                'type' => 'int',
                'validate' => 'required|int',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
