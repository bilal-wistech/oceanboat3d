<?php

Route::group(['namespace' => 'Botble\Theme\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'theme'], function () {
            Route::get('all', [
                'as' => 'theme.index',
                'uses' => 'ThemeController@index',
            ]);

            Route::post('active', [
                'as' => 'theme.active',
                'uses' => 'ThemeController@postActivateTheme',
                'permission' => 'theme.index',
            ]);

            Route::post('remove', [
                'as' => 'theme.remove',
                'uses' => 'ThemeController@postRemoveTheme',
                'middleware' => 'preventDemo',
                'permission' => 'theme.index',
            ]);
        });

        Route::group(['prefix' => 'theme/options'], function () {
            Route::get('', [
                'as' => 'theme.options',
                'uses' => 'ThemeController@getOptions',
            ]);

            Route::post('', [
                'as' => 'theme.options.post',
                'uses' => 'ThemeController@postUpdate',
                'permission' => 'theme.options',
            ]);
        });

        Route::group(['prefix' => 'theme/parallel-slider'], function () {
            Route::match(['post','get'],'/', [
                'as' => 'theme.parallel-slider',
                'uses' => 'ParallelSliderController@index',
            ]);
            Route::match(['post','get'],'create', [
              'as' => 'parallel-slider.create',
              'uses' => 'ParallelSliderController@create',
            ]);
            Route::match(['post','get'],'/store', [
              'as' => 'parallel-slider.store',
              'uses' => 'ParallelSliderController@store',
            ]);
            Route::match(['post','get'],'/status/{id}', [
              'as' => 'parallel-slider.status.{id}',
              'uses' => 'ParallelSliderController@status',
            ]);
            Route::match(['post','get'],'/update/{id}', [
              'as' => 'parallel-slider.update',
              'uses' => 'ParallelSliderController@update',
            ]);
            Route::match(['post','get'],'/edit/{id}', [
              'as' => 'parallel-slider.edit',
              'uses' => 'ParallelSliderController@edit',
            ]);
            Route::delete('/delete/{id}', [
              'as' => 'parallel-slider.destroy',
              'uses' => 'ParallelSliderController@destroy',
            ]);
            Route::delete('/delete', [
              'as' => 'parallel-slider.deletes',
              'uses' => 'ParallelSliderController@deletes',
            ]);

        });
        Route::group(['prefix' => 'theme/video-background'], function () {
            Route::match(['post','get'],'/', [
                'as' => 'theme.video-background',
                'uses' => 'VideoBackgroundController@index',
            ]);
            Route::match(['post','get'],'create', [
              'as' => 'video-background.create',
              'uses' => 'VideoBackgroundController@create',
            ]);
            Route::match(['post','get'],'/store', [
              'as' => 'video-background.store',
              'uses' => 'VideoBackgroundController@store',
            ]);
            Route::match(['post','get'],'/status/{id}', [
              'as' => 'video-background.status.{id}',
              'uses' => 'VideoBackgroundController@status',
            ]);
            Route::match(['post','get'],'/update/{id}', [
              'as' => 'video-background.update',
              'uses' => 'VideoBackgroundController@update',
            ]);
            Route::match(['post','get'],'/edit/{id}', [
              'as' => 'video-background.edit',
              'uses' => 'VideoBackgroundController@edit',
            ]);
            Route::delete('/delete/{id}', [
              'as' => 'video-background.destroy',
              'uses' => 'VideoBackgroundController@destroy',
            ]);
            Route::delete('/delete', [
              'as' => 'video-background.deletes',
              'uses' => 'VideoBackgroundController@deletes',
            ]);

        });

        Route::group(['prefix' => 'theme/custom-css'], function () {
            Route::get('', [
                'as' => 'theme.custom-css',
                'uses' => 'ThemeController@getCustomCss',
            ]);

            Route::post('', [
                'as' => 'theme.custom-css.post',
                'uses' => 'ThemeController@postCustomCss',
                'permission' => 'theme.custom-css',
                'middleware' => 'preventDemo',
            ]);
        });

        Route::group(['prefix' => 'theme/custom-js'], function () {
            Route::get('', [
                'as' => 'theme.custom-js',
                'uses' => 'ThemeController@getCustomJs',
            ]);

            Route::post('', [
                'as' => 'theme.custom-js.post',
                'uses' => 'ThemeController@postCustomJs',
                'permission' => 'theme.custom-js',
                'middleware' => 'preventDemo',
            ]);
        });

        Route::group(['prefix' => 'theme/custom-html'], function () {
            Route::get('', [
                'as' => 'theme.custom-html',
                'uses' => 'ThemeController@getCustomHtml',
            ]);

            Route::post('', [
                'as' => 'theme.custom-html.post',
                'uses' => 'ThemeController@postCustomHtml',
                'permission' => 'theme.custom-html',
                'middleware' => 'preventDemo',
            ]);
        });
    });
});
