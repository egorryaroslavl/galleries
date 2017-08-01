<?php

	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;

	class CreateGalleriesTable extends Migration
	{

		public function up()
		{
			Schema::create( 'galleries', function ( Blueprint $table ){
				$table->increments( 'id' )->unsigned();
				$table->string( 'name' );
				$table->string( 'alias' );
				$table->text( 'description' )->nullable();
				$table->string( 'icon' )->nullable();
				$table->text( 'gallery' )->nullable();
				$table->boolean( 'public' )->default( 1 );
				$table->boolean( 'anons' )->default( 0 );
				$table->boolean( 'hit' )->default( 0 );
				$table->integer( 'pos' )->default( 0 );
				$table->string( 'h1' )->nullable();
				$table->string( 'metatag_title' )->nullable();
				$table->string( 'metatag_description' )->nullable();
				$table->string( 'metatag_keywords' )->nullable();
				$table->timestamps();
			} );
		}


		public function down()
		{
			//
		}
	}
