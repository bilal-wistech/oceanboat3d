<?php
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'NaeemAwan\PredefinedLists\Http\Controllers', 'middleware' => ['web', 'core']], function () {
  Route::match(['post','get'],'/customize-boat', [
    'as' => 'public.customize-boat',
    'uses' => 'PublicProductController@getProducts',
  ]);
  Route::match(['post','get'],'/customize-boat/store', [
    'as' => 'public.customize-boat.submit',
    'uses' => 'PublicProductController@SubmitBoat',
  ]);
  Route::match(['post','get'],'/customize-boat/{id}', [
    'as' => 'public.customize-boat.id',
    'uses' => 'PublicProductController@getProduct',
  ]);
  Route::match(['post','get'],'/customize-type-content', [
    'uses' => 'PublicProductController@getTypeContent',
  ]);
  // payments
  Route::match(['post','get'],'/transaction/success', [
    'as' => 'ngenius.transaction.success',
    'uses' => 'NgeniusPaymentController@success',
  ]);
  Route::match(['post','get'],'/accessories/success', [
    'as' => 'ngenius.accessories.success',
    'uses' => 'NgeniusPaymentController@accessoriessuccess',
  ]);
  Route::match(['post','get'],'/transaction/{id}', [
    'as' => 'ngenius.transaction.id',
    'uses' => 'NgeniusPaymentController@createtransaction',
  ]);
  Route::match(['post','get'],'/accessories/{id}', [
    'as' => 'ngenius.accessories.id',
    'uses' => 'NgeniusPaymentController@createtransaction',
  ]);
  // dhl
  Route::match(['post','get'],'/get-rate', [
    'uses' => 'DHLController@rates',
  ]);
  Route::match(['post','get'],'/create-shipment', [
    'as' => 'create.shipment',
    'uses' => 'DHLController@shipment',
  ]);

    Route::match(['post','get'],'/create-awb-slip/{id}', [
    'as' => 'awb_slip.create',
    'uses' => 'DHLController@generateAWBSlip',
  ]);
  
  Route::match(['post','get'],'/track-order/{id}', [
    'as' => 'track.order',
    'uses' => 'DHLController@track',
  ]);
});

Route::group(['namespace' => 'NaeemAwan\PredefinedLists\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    //products
    Route::group(['prefix' => BaseHelper::getAdminPrefix(). '/predefined-list' , 'middleware' => 'auth'], function () {
    		Route::match(['post','get'],'/', [
          'as' => 'predefined-list',
          'uses' => 'PredefinedListController@index',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'create', [
          'as' => 'predefined-list.create',
          'uses' => 'PredefinedListController@create',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/create/{parent}', [
          'as' => 'predefined-list.create.parent',
          'uses' => 'PredefinedListController@create',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/store', [
          'as' => 'predefined-list.store',
          'uses' => 'PredefinedListController@store',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/status/{id}', [
          'as' => 'predefined-list.status.{id}',
          'uses' => 'PredefinedListController@status',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/update/{id}', [
          'as' => 'predefined-list.update',
          'uses' => 'PredefinedListController@update',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/edit/{id}', [
          'as' => 'predefined-list.edit',
          'uses' => 'PredefinedListController@edit',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::delete('/delete/{id}', [
          'as' => 'predefined-list.destroy',
          'uses' => 'PredefinedListController@destroy',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::delete('/delete', [
          'as' => 'predefined-list.deletes',
          'uses' => 'PredefinedListController@deletes',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/{parent}', [
          'as' => 'predefined-list.parent',
          'uses' => 'PredefinedListController@index',
          'permission' => 'plugins.ecommerce',
        ]);
   	});
    //categories
    Route::group(['prefix' => BaseHelper::getAdminPrefix(). '/predefined-categories' , 'middleware' => 'auth'], function () {
        Route::match(['post','get'],'/', [
          'as' => 'predefined-categories',
          'uses' => 'PredefinedCategoryController@index',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'create', [
          'as' => 'predefined-categories.create',
          'uses' => 'PredefinedCategoryController@create',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/store', [
          'as' => 'predefined-categories.store',
          'uses' => 'PredefinedCategoryController@store',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/status/{id}', [
          'as' => 'predefined-categories.status.{id}',
          'uses' => 'PredefinedCategoryController@status',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/update/{id}', [
          'as' => 'predefined-categories.update',
          'uses' => 'PredefinedCategoryController@update',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/edit/{id}', [
          'as' => 'predefined-categories.edit',
          'uses' => 'PredefinedCategoryController@edit',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::delete('/delete/{id}', [
          'as' => 'predefined-categories.destroy',
          'uses' => 'PredefinedCategoryController@destroy',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::delete('/delete', [
          'as' => 'predefined-categories.deletes',
          'uses' => 'PredefinedCategoryController@deletes',
          'permission' => 'plugins.ecommerce',
        ]);
    });

    //enquiries
    Route::group(['prefix' => BaseHelper::getAdminPrefix(). '/custom-boat-enquiries' , 'middleware' => 'auth'], function () {
        Route::match(['post','get'],'/', [
          'as' => 'custom-boat-enquiries',
          'uses' => 'BoatEnquiryController@index',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/edit/{id}', [
          'as' => 'custom-boat-enquiries.edit',
          'uses' => 'BoatEnquiryController@edit',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::match(['post','get'],'/update/{id}', [
          'as' => 'custom-boat-enquiries.update',
          'uses' => 'BoatEnquiryController@update',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::delete('/delete/{id}', [
          'as' => 'custom-boat-enquiries.destroy',
          'uses' => 'BoatEnquiryController@destroy',
          'permission' => 'plugins.ecommerce',
        ]);
        Route::delete('/delete', [
          'as' => 'custom-boat-enquiries.deletes',
          'uses' => 'BoatEnquiryController@deletes',
          'permission' => 'plugins.ecommerce',
        ]);
    });

});

