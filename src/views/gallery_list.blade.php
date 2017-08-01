@isset($data)
	@if(count($data)>0)
		@foreach($data as $item)
			<div class="col-md-3" id="thumbnail-{{$item->imgid}}">
				<div class="ibox-content text-center">
					<div class="m-b-sm preview" style="position:relative;">
						<button
							class="btn btn-danger btn-circle btn-sm image-del"
							title="Удалить"
							data-imgid="{{$item->imgid}}"
							data-thumbnail="{{$item->thumbnail}}"
							data-status="{{$item->status or 'old'}}"
							type="button" data-dz-remove><i class="fa fa-trash"></i></button>
						<div>
							<img
								alt=""
								src="/"
								id="img-thumbnail-{{$item->imgid}}"
								class="img-thumbnail img-responsive gallery-image"
								title="Пропрции изображения-превью могут не совпадать с пропорциями исходного изображения"
								style="background-image:url({{$item->dataurl or  '/' . config( 'admin.galleries.gallery_dir' ). $item->thumbnail}})">
							
							<input type="text" name="gallery[{{$item->imgid}}][comment]"
							       value="{{$item->comment or ''}}"
							       class="image-comment">
							{{--	<a
									class="btn btn-info btn-bitbucket btn-xs"
									data-imgid="{{$item->imgid}}"
									data-status="{{$item->status or 'old'}}"
									title="Сохранить"
									style="font-size: 10px">
									<i class="fa fa-save"></i> Сохранить
								</a>--}}
							<a class="btn btn-warning btn-bitbucket btn-xs" title="Очистить комментарий"
							   style="font-size: 10px" onclick="$(this).prev().val('')">
								<i class="fa fa-remove"></i> Очистить
							</a>
						
						</div>
						@if(isset($item->status) && $item->status == 'new' )
							<input type="hidden" name="gallery[{{$item->imgid}}][status]"
							       value="{{$item->status}}"/>
						@endif
						<input type="hidden" name="gallery[{{$item->imgid}}][imgid]" value="{{$item->imgid}}"/>
						<input type="hidden" name="gallery[{{$item->imgid}}][name]" value="{{$item->name}}"/>
						
						<input type="hidden" name="gallery[{{$item->imgid}}][thumbnail]"
						       value="{{$item->thumbnail}}"/>
					
					</div>
				</div>
			</div>
		@endforeach
		<style>.gallery-image{width:150px;height:150px;background-position:50% 50%;background-repeat:no-repeat;background-size:cover}.image-del{position:absolute;top:0;right:0}.image-comment{height:4rem;width:150px;border:1px #ddd solid;text-align:center;padding:2px;margin:3px 0;font-size:12px;line-height:1rem}</style>
	@endif
@endisset
