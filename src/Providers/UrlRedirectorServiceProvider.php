<?php

namespace ArchiElite\UrlRedirector\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use ArchiElite\UrlRedirector\Models\UrlRedirector;
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

        DashboardMenu::default()->beforeRetrieving(function () {
            DashboardMenu::registerItem([
                'id' => 'cms-plugins-url_redirector',
                'priority' => 910,
                'name' => 'plugins/url-redirector::url-redirector.menu',
                'icon' => 'ti ti-external-link',
                'route' => 'url-redirector.index',
            ]);
        });

        $this->app['events']->listen(RouteMatched::class, function () {
            $this->app[ExceptionHandler::class]->renderable(function (Throwable $throwable, Request $request) {
                if ($throwable instanceof NotFoundHttpException) {
                    $url = UrlRedirector::query()->where('original', $request->url())->first();

                    if ($url) {
                        $url->increment('visits');

                        return redirect()->to($url->target, 301);
                    }
                }
            });
        });
    }
}
