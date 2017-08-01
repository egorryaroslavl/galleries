<?php

	namespace Egorryaroslavl\Galleries;

	use Illuminate\Support\ServiceProvider;

	class GalleriesServiceProvider extends ServiceProvider
	{

		public function boot()
		{
		$this->loadViewsFrom( __DIR__ . '/views', 'galleries' );
		$this->loadRoutesFrom( __DIR__ . '/routes.php' );

	/*		$this->publishes( [ __DIR__ . '/views' => resource_path( 'views/admin/galleries' ) ], 'galleries' );
			$this->publishes( [ __DIR__ . '/config/galleries.php' => config_path( '/admin/galleries.php' ) ], 'galleriesConfig' );
			$this->publishes( [
				__DIR__ . '/migrations/2017_07_19_060856_create_galleries_table.php' => base_path( 'database/migrations/2017_07_19_060856_create_galleries_table.php' )
			], '' );*/


		}

		public function register()
		{

	 		$this->app->make( 'Egorryaroslavl\Galleries\GalleriesController' );
			$this->app->make( 'Egorryaroslavl\Galleries\ImagesController' );
			$this->mergeConfigFrom( __DIR__ . '/config/galleries.php', 'galleries' );
		}

	}