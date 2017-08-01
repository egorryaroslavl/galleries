<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.1.1/min/dropzone.min.js"></script>
<div class="" id="previews" style="margin-left: -800px">
	<div id="template"></div>
</div>
<div id="gallery"></div>
<script>
	var previewNode = document.querySelector( "#template" );
	previewNode.id = "";
	var previewTemplate = previewNode.parentNode.innerHTML;
	previewNode.parentNode.removeChild( previewNode );
	new Dropzone( '#previews', {
		url                  : "/uploadImages",
		paramName            : "photo",
		createImageThumbnails: false,
		uploadMultiple       : true,
		parallelUploads      : 20,
		params               : {
			table : "{{$data->table}}",
			id    : "{{$data->id or "0"}}",
			_token: "{{csrf_token()}}"
		},
		previewTemplate      : previewTemplate,
		clickable            : ".fileinput-button",
		processingmultiple   : function(){
			$( '#gallery' ).prepend( '<p class="preload"><i class="fa fa-spinner fa-spin fa-lg"></i>\n' +
				'<span class="sr-only"> Идёт загрузка...</span>  Идёт загрузка...</p>' );
		},
		successmultiple      : function( file, msg ){
			$( '.preload' ).remove();
			$( '#gallery' ).addClass( 'animated fadeInDown' ).append( msg );
		}
	} );

	$( function(){

		if( $( "input[name='id']" ).length > 0 ){
			$( "#gallery" ).load( '/loadGallery', { 'id':{{$data->id or "0"}}, _token:"{{csrf_token()}}" } );
		}


		$( "body" ).on( "click", ".image-del", function(){
			var _this = $( this );
			var imgid = _this.data( 'imgid' );
			var thumbnail = _this.data( 'thumbnail' );
			var status = _this.data( 'status' );
			if( confirm( "Изображение будет удалено!\nПродолжить?" ) ){

				if( status === 'old' ){

					$.ajax( {
						type   : "POST",
						url    : "/imagedelete",
						data   : {
							imgid    : imgid,
							id       :{{$data->id or "0"}},
							status   : status,
							thumbnail: thumbnail,
							_token   : "{{csrf_token()}}"
						},
						success: function( msg ){

						}
					} );
				}
				var thumb = $( "#thumbnail-" + imgid );
				thumb.addClass( 'animated fadeOutDown' );
				setTimeout( function(){
					thumb.remove();
				}, 600 );
			}
		} );
	} );
</script>
<style>
	.preload {
		text-align: center;
		font-size:  26px
		}
</style>