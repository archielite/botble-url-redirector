<?php

namespace ArchiElite\UrlRedirector\Tables;

use ArchiElite\UrlRedirector\Models\UrlRedirector;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\LinkableColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class UrlRedirectorTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(UrlRedirector::class)
            ->addHeaderAction(CreateHeaderAction::make()->url(route('url-redirector.create')))
            ->addColumns([
                IdColumn::make(),
                Column::make('original')
                    ->label(trans('plugins/url-redirector::url-redirector.original')),
                LinkableColumn::make('target')
                    ->label(trans('plugins/url-redirector::url-redirector.target'))
                    ->externalLink(),
                Column::make('visits')
                    ->label(trans('plugins/url-redirector::url-redirector.visits')),
            ])
            ->addActions([
                EditAction::make()->route('url-redirector.edit'),
                DeleteAction::make()->route('url-redirector.destroy'),
            ])
            ->addBulkAction(DeleteBulkAction::make())
            ->addBulkChanges([
                NameBulkChange::make()
                    ->name('original')
                    ->title(trans('plugins/url-redirector::url-redirector.original')),
                NameBulkChange::make()
                    ->name('target')
                    ->title(trans('plugins/url-redirector::url-redirector.target')),
            ])
            ->queryUsing(function (EloquentBuilder $query) {
                return $query
                    ->select([
                        'id',
                        'original',
                        'target',
                        'visits',
                    ]);
            });
    }
}
