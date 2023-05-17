<?php

namespace ArchiElite\UrlRedirector\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use ArchiElite\UrlRedirector\Repositories\Interfaces\UrlRedirectorInterface;
use ArchiElite\UrlRedirector\Models\UrlRedirector;
use ArchiElite\UrlRedirector\Repositories\Caches\UrlRedirectorCacheDecorator;
use ArchiElite\UrlRedirector\Repositories\Eloquent\UrlRedirectorRepository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class UrlRedirectorServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(UrlRedirectorInterface::class, function () {
            return new UrlRedirectorCacheDecorator(new UrlRedirectorRepository(new UrlRedirector()));
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/url-redirector')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadMigrations();

        $this->app['events']->listen(RouteMatched::class, function () {
            DashboardMenu::registerItem([
                'id' => 'cms-plugins-url',
                'priority' => 10000,
                'parent_id' => 'cms-core-settings',
                'name' => 'plugins/url-redirector::url-redirector.menu',
                'url' => route('url-redirector.index'),
                'permissions' => ['url-redirector.index'],
            ]);

            $this->app[ExceptionHandler::class]->renderable(function (Throwable $throwable, Request $request) {
                if ($throwable instanceof NotFoundHttpException) {
                    $url = UrlRedirector::query()->where('original', $request->fullUrl())->first();

                    if ($url) {
                        $url->increment('visits');

                        return redirect()->to($url->target, 301);
                    }
                }
            });
        });
    }
}
