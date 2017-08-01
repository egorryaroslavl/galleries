<?php

	namespace Egorryaroslavl\Galleries;

	use App\Http\Controllers\Controller;
	use Egorryaroslavl\Galleries\Models\GalleryModel;
	use Egorryaroslavl\Galleries\ImagesController;
	use Illuminate\Http\Request;
	use Illuminate\Validation\Rule;


	class GalleriesController extends Controller
	{
		function messages()
		{

			return [
				'name.required'  => 'Поле "Имя" обязятельно для заполнения!',
				'alias.required' => 'Поле "Алиас" обязятельно для заполнения!',
				'name.unique'    => 'Значение поля "Имя" не является уникальным!',
				'alias.unique'   => 'Значение поля "Алиас" не является уникальным!'
			];

		}

		/**
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
		 */
		public function index()
		{
			$data        = GalleryModel::orderBy('pos')->paginate( 30 );
			$data->table = 'galleries';
			$breadcrumbs = '<div class="row wrapper border-bottom white-bg page-heading"><div class="col-lg-12"><h2>Галереи</h2><ol class="breadcrumb"><li><a href="/admin">Главная</a></li><li class="active"><a href="/admin/galleries">Галереи</a></li></ol></div></div>';

			return view( 'galleries::index', [ 'data' => $data, 'breadcrumbs' => $breadcrumbs ] );

		}


		/**
		 * @param \Illuminate\Http\Request $request
		 *
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
		 */
		public function edit( Request $request )
		{


			$data      = GalleryModel::find( $request->id );
			$data->act = 'galleries-update';

			$data->table = 'galleries';
			$breadcrumbs = '<div class="row wrapper border-bottom white-bg page-heading"><div class="col-lg-12"><h2>Галереи</h2><ol class="breadcrumb"><li><a href="/admin">Главная</a></li><li class="active"><a href="/admin/galleries">Галереи</a></li></ol></div></div>';


			return view( 'galleries::form', [ 'data' => $data, 'breadcrumbs' => $breadcrumbs ] );

		}

		/**
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
		 */
		public function create()
		{

			$data      = new GalleryModel();
			$data->act = 'galleries-store';
			return view( 'galleries::form', [ 'data' => $data ] );

		}

		/**
		 * @param \Illuminate\Http\Request $request
		 *
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
		 */
		public function uploadImages( Request $request )
		{

			$images = new ImagesController( $request );
			$data   = $images->uploadImages();
			return view( 'galleries::gallery_list', [ 'data' => (object)$data ] );

		}


		public function loadGallery( Request $request )
		{

			$galleries       = GalleryModel::find( $request->id );
			$resultArray     = $this->prepareArray( $galleries->gallery );
			$imageController = new ImagesController( $request );
			$cleanedArray    = $imageController->CheckingFilesInDirectory( $resultArray );
			return view( 'galleries::gallery_list', [ 'data' => (object)$cleanedArray ] );

		}


		/**
		 * @param \Illuminate\Http\Request $request
		 *
		 * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
		 */
		public function store( Request $request )
		{

			$v = \Validator::make( $request->all(), [
				'name'  => 'required|unique:galleries|max:255',
				'alias' => 'required|unique:galleries|max:255',
			], $this->messages() );

			if( $v->fails() ){
				return redirect( 'admin/galleries/create' )
					->withErrors( $v )
					->withInput();
			}

			$input        = $request->all();
			$input        = array_except( $input, '_token' );
			$galleryModel = GalleryModel::create( $input );
			$id           = $galleryModel->id;
			$images       = new  ImagesController( $request );
			$images->galleryId( $id );
			$galleryArray          = $images->moveImagesFromTmp();
			$galleryModel          = GalleryModel::find( $id );
			$galleryModel->gallery = $galleryArray;
			$galleryModel->save();

			\Session::flash( 'message', 'Запись добавлена!' );

			if( isset( $request->submit_button_stay ) ){
				return redirect()->back();
			}
			return redirect( '/admin/galleries' );
		}

		/**
		 * @param \Illuminate\Http\Request $request
		 *
		 * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
		 */
		public function update( Request $request )
		{

			//dd( $request->all() );

			/* Определяем куда редиректить после выполнения */
			$direct = isset( $request->submit_button_stay ) ? 'stay' : 'back';

			$v = \Validator::make( $request->all(), [
				'name' => [
					'required',
					Rule::unique( 'galleries' )->ignore( $request->id ),
					'max:255'
				],

				'alias' => [
					'required',
					Rule::unique( 'galleries' )->ignore( $request->id ),
					'max:255'
				]


			], $this->messages() );


			/* если есть ошибки - сообщаем об этом */
			if( $v->fails() ){
				return redirect( 'admin/galleries/' . $request->id . '/edit' )
					->withErrors( $v )
					->withInput();
			}

			$images = new  ImagesController( $request );
			$images->galleryId( $request->id );
			$galleryArray = $images->imageProcessing();

			$gallery = GalleryModel::find( $request->id );

			$gallery->name                = $request->name;
			$gallery->alias               = $request->alias;
			$gallery->description         = $request->description;
			$gallery->public              = isset( $request->public ) ? $request->public : 0;
			$gallery->anons               = isset( $request->anons ) ? $request->anons : 0;
			$gallery->hit                 = isset( $request->hit ) ? $request->hit : 0;
			$gallery->gallery             = $galleryArray;
			$gallery->h1                  = $request->h1;
			$gallery->metatag_title       = $request->metatag_title;
			$gallery->metatag_description = $request->metatag_description;
			$gallery->metatag_keywords    = $request->metatag_keywords;
			$gallery->save();

			\Session::flash( 'message', 'Запись обновлена!' );

			if( $direct == 'back' ){
				return redirect( url( '/admin/galleries' ) );
			}

			if( $direct == 'stay' ){
				return redirect()->back();
			}


		}

		/**
		 * @param $fileName
		 *
		 * @return array
		 */
		public function parseName( $fileName )
		{
			$baseName = basename( $fileName );
			$ext      = substr( $baseName, strrpos( $baseName, '.', -1 ), strlen( $baseName ) );
			$baseName = str_replace( $ext, '', $baseName );

			$fileNameParts = explode( '_', $baseName );

			return [
				'table_name' => $fileNameParts[ 0 ],
				'token'      => $fileNameParts[ 1 ],
				'imgid'      => $fileNameParts[ 2 ]
			];

		}

		public function prepareArray( $galleriesArray )
		{

			if( count( $galleriesArray ) > 0 ){

				foreach( $galleriesArray as $gallery ){


					$resultAr[] = (object)[
						'name'      => $gallery[ 'name' ],
						'thumbnail' => $gallery[ 'thumbnail' ],
						'comment'   => $gallery[ 'comment' ],
						'imgid'     => $gallery[ 'imgid' ],

					];

				}

				return $resultAr;

			}

		}

		public function imagedelete( Request $request )
		{

			$thumbnail = $request->thumbnail;
			$name      = str_replace( '_small', '', $request->thumbnail );
			$path      = config( 'admin.galleries.gallery_dir' );

			$thumbnailPath = public_path( $path . $thumbnail );
			$filePath      = public_path( $path . $name );

			if( file_exists( $thumbnailPath ) && !is_dir( $thumbnailPath ) ){
				unlink( $thumbnailPath );
			}

			if( file_exists( $filePath ) && !is_dir( $filePath ) ){
				unlink( $filePath );
			}


		}


		public function destroy( $id )
		{

			$galleryModel = GalleryModel::find( $id );
			$gallery      = $galleryModel[ 'gallery' ];
			$galleriesDir = config( 'admin.galleries.gallery_dir' );

			if( count( $gallery ) > 0 ){

				foreach( $gallery as $item ){

					$name      = public_path( $galleriesDir . $item[ 'name' ] );
					$thumbnail = public_path( $galleriesDir . $item[ 'thumbnail' ] );

					if( file_exists( $name ) ){
						unlink( $name );
					}

					if( file_exists( $thumbnail ) ){
						unlink( $thumbnail );
					}
				}
			}

			$galleryModel->delete();

			return redirect()->back();

		}


		public function reorder( Request $request )
		{


			if( isset( $request->sort_data ) ){

				$id        = array();
				$table     = $request->table;
				$sort_data = $request->sort_data;

				parse_str( $sort_data );

				$count = count( $id );
				for( $i = 0; $i < $count; $i++ ){
					\DB::update( 'UPDATE `' . $table . '` SET `pos`=' . $i . ' WHERE `id`=? ', [ $id[ $i ] ] );

				}


			}
		}


	}
