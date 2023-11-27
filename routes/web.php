<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'ArchiElite\UrlRedirector\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {
        Route::group(['prefix' => 'url-redirector', 'as' => 'url-redirector.'], function () {
            Route::resource('', 'UrlRedirectorController')->parameters(['' => 'url']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'UrlRedirectorController@deletes',
                'permission' => 'url-redirector.destroy',
            ]);
        });
    });
});
