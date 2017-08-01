<?php

	use Illuminate\Http\Request;

	/*=============  GALLERIES  ==============*/

	Route::group( [ 'middleware' => 'web' ], function (){

		Route::get( '/admin/galleries', 'egorryaroslavl\galleries\GalleriesController@index' );
		Route::get( '/admin/galleries/create', 'egorryaroslavl\galleries\GalleriesController@create' );
		Route::get( '/admin/galleries/{id}/edit', 'egorryaroslavl\galleries\GalleriesController@edit' );
		Route::post( '/admin/galleries/store', 'egorryaroslavl\galleries\GalleriesController@store' )
			->name( 'galleries-store' );
		Route::post( '/admin/galleries/update', 'egorryaroslavl\galleries\GalleriesController@update' )->name( 'galleries-update' );
		Route::get( '/admin/galleries/{id}/delete', 'egorryaroslavl\galleries\GalleriesController@destroy' );


		Route::post( '/translite', function ( Request $request ){
			return json_encode( [ 'alias' => str_slug( strtolower( $request->alias_source ), '_' ) ] );
		} );
		Route::post( '/loadGallery', 'egorryaroslavl\galleries\GalleriesController@loadGallery' );
		Route::post( '/uploadImages', 'egorryaroslavl\galleries\GalleriesController@uploadImages' );
		Route::post( '/imagedelete', 'egorryaroslavl\galleries\GalleriesController@imagedelete' )->name( 'image-delete' );
		Route::post( '/changestatus', 'egorryaroslavl\galleries\GalleriesController@changestatus' )->name( 'changestatus' );
		Route::post( '/reorder', 'egorryaroslavl\galleries\GalleriesController@reorder' )->name( 'reorder' );


	} );

	/*=============  /GALLERIES  ==============*/

