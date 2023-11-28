<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;
use ArchiElite\UrlRedirector\Http\Controllers\UrlRedirectorController;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'url-redirector', 'as' => 'url-redirector.'], function () {
        Route::resource('', UrlRedirectorController::class)->parameters(['' => 'url']);
    });
});
