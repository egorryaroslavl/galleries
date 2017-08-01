@extends('admin.layouts.basic')
@section('content')
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox">
					<h5>Галереи</h5>
					<a href="/admin/{{$data->table}}/create" class="btn btn-primary"><i
							class="fa fa-plus"></i> Добавить запись</a><br><br>
					<div class="ibox-content">
						@if (Session::has('message'))
							<div class="alert alert-info">{!! Session::get('message') !!}</div>
						@endif
						 {{$data->render()}}
						<table
							class="footable table table-stripped toggle-arrow-tiny default breakpoint footable-loaded"
							data-page-size="15">
							<thead>
							<tr>
								<th style="width: 20px"><i class="fa fa-random"></i></th>
								<th>Имя</th>
								<th>Статус</th>
								<th class="text-right" data-sort-ignore="true">Действия</th>
							</tr>
							</thead>
							<tbody id="sortable" data-table="{{$data->table or ''}}">
							@if(  count( $data ) > 0 )
								@foreach( $data as $item )
									<tr class="ui-state-default" id="id_{{$item->id}}">
										<td class="reorder"><i class="fa fa-ellipsis-v"></i> <i
												class="fa fa-ellipsis-v"></i>
										</td>
										<td
											id="name_public_{{$item->id}}"
											style="max-width:30.0rem;overflow: hidden">
											<a href="/admin/{{$data->table}}/{{$item->id}}/edit">
												 {{$item->name }}</a>
										</td>
										<td>
											@include('admin.common.status_buttons_set')
										</td>
										<td class="text-right" style="min-width:10.0rem">
											@include('admin.common.edit_delete_buttons_set')
										</td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="4"><i class="fa fa-ban"></i>  Пусто...</td>
								</tr>
							@endif
							</tbody>
							<tfoot>
							<tr>
								<td colspan="6">
									 {{$data->render()}}
								</td>
							</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="placeModal"></div>
@endsection
