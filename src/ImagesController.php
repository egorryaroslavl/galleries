<?php

	namespace Egorryaroslavl\Galleries;

	use App\Http\Controllers\Controller;
	use Egorryaroslavl\Galleries\Models\GalleryModel;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Validation\Rule;
	use Illuminate\Support\Facades\File;
	use Intervention\Image\Facades\Image;


	class ImagesController extends Controller
	{
		private $galleryId;
		private $request;
		private $imageWidth;
		private $imageHeight;
		private $imageThumbnailWidth;
		private $imageThumbnailHeight;
		private $galleriesDir;

		function __construct( Request $request )
		{
			$this->request              = $request;
			$this->imageWidth           = config( 'admin.galleries.image_width', 1024 );
			$this->imageHeight          = config( 'admin.galleries.image_height', 1024 );
			$this->imageThumbnailWidth  = config( 'admin.galleries.image_preview_width', 220 );
			$this->imageThumbnailHeight = config( 'admin.galleries.image_preview_height', 220 );
			$this->galleriesDir         = config( 'admin.galleries.gallery_dir' );
			$this->galleryDirChek();
		}


		/**
		 * ID галереи
		 *
		 * @param $galleryId
		 *
		 * @return mixed
		 */
		public function galleryId( $galleryId )
		{
			return $this->galleryId = $galleryId;
		}

		private function galleryDirChek()
		{
			$path = public_path() . '/' . config( 'admin.galleries.gallery_dir' );
			if( !File::isDirectory( $path ) ){
				File::makeDirectory( $path, 0775 );
			}


		}


		/**
		 * @return array
		 */
		function uploadImages()
		{
			/* если файлы имеются */
			if( $this->request->hasFile( 'photo' ) ){
				$files      = $this->request->file( 'photo' );
				$filesCount = count( $files );

				if( $filesCount > 0 ){
					/* в POST проверяем наличие и значение id */

					if( isset( $request->id ) && intval( $this->request->id ) > 0 ){
						/* если id передан, обрабатываем изображение и сразу кладём в директорию в public */

					} else{
						/* если id отсутствует, значит галерея ещё не сохранялась, обрабатываем изображение и кладём в /tmp */

						return $this->saveToTmp();

					}


				}

			}

		}

		public function moveImagesFromTmp()
		{

			$resultArray = [];
			$uploads_dir = sys_get_temp_dir(); // системный /tmp

			if( isset( $this->request->gallery ) ){

				$galleries = $this->request->gallery;

				if( count( $galleries ) > 0 ){

					foreach( $galleries as $gallery ){

						if( isset( $gallery[ 'status' ] ) && $gallery[ 'status' ] == 'new' ){

							$name      = str_replace( $this->request->_token, $this->galleryId, $gallery[ 'name' ] );
							$thumbnail = str_replace( $this->request->_token, $this->galleryId, $gallery[ 'thumbnail' ] );

							$filePath = $uploads_dir . '/' . $gallery[ 'name' ];

							$fileSmallPath = $uploads_dir . '/' . $gallery[ 'thumbnail' ];


							Image::make( $filePath )
								->save( public_path( $this->galleriesDir . $name ) );
							Image::make( $fileSmallPath )
								->save( public_path( $this->galleriesDir . $thumbnail ) );


							if( file_exists( $this->galleriesDir . $name )
								&& file_exists( $this->galleriesDir . $thumbnail ) ){


								$resultArray[] = [
									'imgid'     => $gallery[ 'imgid' ],
									'name'      => $name,
									'thumbnail' => $thumbnail,
									'comment'   => $gallery[ 'comment' ]

								];
							}


						}
						unset( $imgid, $gallery );
					}

				}

				return $resultArray;

			}

		}

		function getFromTmp( $gallery )
		{
			$uploads_dir = sys_get_temp_dir(); // системный /tmp
			$resultArray = [];

			if( isset( $gallery[ 'status' ] ) && $gallery[ 'status' ] == 'new' ){

				$name      = str_replace( $this->request->_token, $this->galleryId, $gallery[ 'name' ] );
				$thumbnail = str_replace( $this->request->_token, $this->galleryId, $gallery[ 'thumbnail' ] );

				$filePath = $uploads_dir . '/' . $gallery[ 'name' ];

				$fileSmallPath = $uploads_dir . '/' . $gallery[ 'thumbnail' ];


				Image::make( $filePath )
					->save( public_path( $this->galleriesDir . $name ) );
				Image::make( $fileSmallPath )
					->save( public_path( $this->galleriesDir . $thumbnail ) );

				$resultArray = [
					'imgid'     => $gallery[ 'imgid' ],
					'name'      => $name,
					'thumbnail' => $thumbnail,
					'comment'   => $gallery[ 'comment' ]

				];

			}
			return $resultArray;
		}


		/**
		 * @return array
		 */
		function saveToTmp()
		{

			$allowedExt = [ 'jpeg', 'jpg', 'png', 'gif' ];

			if( $this->request->hasFile( 'photo' ) ){

				$resultArray = [];
				$files       = $this->request->file( 'photo' );

				$uploads_dir = sys_get_temp_dir(); // системный /tmp

				foreach( $files as $file ){

					$ext = $file->clientExtension();

					if( in_array( $ext, $allowedExt ) ){


						$imgId = $this->imgId();

						$newName  = $this->newName( $file, $this->request->_token, $imgId );
						$filePath = $uploads_dir . '/' . $newName;


						$newNameSmall  = $this->newNameSmall( $file, $this->request->_token, $imgId );
						$fileSmallPath = $uploads_dir . '/' . $newNameSmall;


						Image::make( $file )
							->save( $filePath )
							->widen( $this->imageWidth, function ( $constraint ){
								$constraint->upsize();
							} )
							->heighten( $this->imageHeight, function ( $constraint ){
								$constraint->upsize();
							} )->save( $filePath );


						$imgSmall = Image::make( $file )
							->save( $fileSmallPath )
							->widen( $this->imageThumbnailWidth, function ( $constraint ){
								$constraint->upsize();
							} )
							->heighten( $this->imageThumbnailHeight, function ( $constraint ){
								$constraint->upsize();
							} )->save( $fileSmallPath );


						$resultArray[] = (object)[
							'status'    => 'new',
							'name'      => $newName,
							'thumbnail' => $newNameSmall,
							'imgid'     => $this->getImgId( $newName ),
							'dataurl'   => (string)$imgSmall->encode( 'data-url' )
						];

					}
				}
				return $resultArray;

			}

		}


		public function imageProcessing()
		{
			$galleriesResultArray = [];

			if( isset( $this->request->gallery ) && count( $this->request->gallery ) > 0 ){

				foreach( $this->request->gallery as $gallery ){

					if( isset( $gallery[ 'status' ] ) && $gallery[ 'status' ] == 'new' ){

						$gallery_               = $this->getFromTmp( $gallery );
						$galleriesResultArray[] = $gallery_;

					} else{

						$galleriesResultArray[] = $gallery;
					}

				}
				return $galleriesResultArray;

			}

			return $galleriesResultArray;

		}


		/**
		 * Генерит уникальный ID изображения
		 *
		 * @return string
		 */
		public function imgId()
		{

			return strtolower(
				str_random( 4 )
				. '-' . str_random( 4 )
				. '-' . str_random( 4 )
				. '-' . str_random( 4 ) );

		}

		/**
		 * @param $file
		 * @param $token
		 *
		 * @return string
		 */
		public function newName( $file, $token, $imgId )
		{
			$ext = $file->clientExtension();
			return 'galleries_' . $token . '_' . $imgId . '.' . $ext; // новое имя файла

		}


		/**
		 * @param $file
		 * @param $token
		 *
		 * @return string
		 */
		public function newNameSmall( $file, $token, $imgId )
		{
			$ext = $file->clientExtension();
			return 'galleries_' . $token . '_' . $imgId . '_small.' . $ext; // новое имя файла

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


		public function CheckingFilesInDirectory( $galleryArray )
		{

			$cleanedArray = [];

			if( count( $galleryArray ) > 0 ){


				foreach( $galleryArray as  $gallery ){

					if( file_exists( public_path( $this->galleriesDir . $gallery->name ) )
						&& file_exists( public_path( $this->galleriesDir . $gallery->thumbnail ) )
					){

						$cleanedArray[] = $gallery;

					}


				}

				return $cleanedArray;


			}


		}


		public function getImgId( $fileName )
		{

			return $this->parseName( $fileName )[ 'imgid' ];

		}


	}