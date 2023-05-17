<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'ArchiElite\UrlRedirector\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
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
