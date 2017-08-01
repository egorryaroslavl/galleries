@extends('admin.layouts.basic')
@section('content')
	@include('admin.common.errors_block')
	{{ Form::open( [ 'route' =>  $data->act ,'enctype'=>'multipart/form-data' ] ) }}
	@if(isset( $data->id ) ) {{ Form::hidden('id',$data->id )}} @endif
	<div class="tabs-container">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#tab-1" onclick="return false">Основные данные</a>
			</li>
			<li><a data-toggle="tab" href="#tab-2" onclick="return false">Галерея</a></li>
			<li><a data-toggle="tab" href="#tab-3" onclick="return false">SEO</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
							{{--	<div class="col-md-3">
									@include('admin.common.icon')
								</div>--}}
								<div class="col-md-12">
										@include('admin.common.name_alias')
									<div class="hr-line-dashed"></div>
									<div class="row">
										<div class="col-xs-12">
											<div class="form-group">
												{{Form::label('description','Текст')}}
												{{Form::textarea('description',$data->description,['class'=>'form-control','id'=>'description' ])}}
											</div>
										</div>
									</div>
									<div class="hr-line-dashed"></div>
									@include('admin.common.public_anons_hit')
									<div class="hr-line-dashed"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="panel-body">
					<button
						type="button"
						class="fileinput-button btn btn-info"
						id="fileinput-button">Выбрать файлы
					</button>
					<hr>
 					@include('galleries::gallery_dropzone' )
				</div>
			</div>
			<div id="tab-3" class="tab-pane">
				<div class="panel-body">
					@include('admin.common.metatag_title_metatag_description_metatag_keywords')
				</div>
			</div>
		</div>
	</div>
	<div class="hr-line-dashed"></div>
	<div class="row">
		<div class="col-xs-12" style="margin-bottom: 60px">
			@include('admin.common.submit_button_with_choice_redirect')
			{!! Form::close() !!}
		</div>
	</div>
@endsection
