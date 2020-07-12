<?php
Route::namespace('\CodexShaper\PWA\Http\Controllers')->group(function(){
	Route::get('/pwa/assets/{path?}', 'PwaController@asset')
        ->where('path', '(.*)')
        ->name('pwa.asset');
	Route::get('serviceworker', 'PwaController@serviceWorker')->name('pwa.serviceworker');
	Route::get('register-serviceworker', 'PwaController@registerServiceWorker')->name('pwa.serviceworker.register');
	Route::get('offline', 'PwaController@offline')->name('pwa.offline');
	Route::group(['prefix' => 'pwa'], function(){
	    Route::get('manifest', 'PwaController@manifest')->name('pwa.manifest');
	});

	// PWA authorisez routes.
	Route::group(['prefix' => 'pwa', 'middleware' => 'auth'], function(){
	    Route::get('', 'PwaController@index')->name('pwa');
	    Route::post('store', 'PwaController@store')->name('pwa.store');
	    Route::put('store', 'PwaController@update')->name('pwa.update');
	    Route::delete('store', 'PwaController@destroy')->name('pwa.delete');
	    Route::post('activate', 'PwaController@activate')->name('pwa.activate');
	    Route::post('deactivate', 'PwaController@deactivate')->name('pwa.deactivate');
	});
});
