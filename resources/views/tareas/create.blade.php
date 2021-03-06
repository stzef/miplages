@extends('layouts.app')

@section('styles')
@endsection


@section('content')

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Tareas</h4>
                </div>
            </div>


			<form class="form-horizontal" id="crear_tarea" >

				<input type="hidden" class="form-control" id="tarea_ctarea" name="tarea[ctarea]" value="@if( $tarea){{ $tarea->ctarea }}@endif">
				<div class="form-group">
					<label for="tarea_ntarea" class="col-sm-2 control-label">Nombre</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="tarea_ntarea" name="tarea[ntarea]" placeholder="Nombre" value="@if( $tarea){{ $tarea->ntarea }}@endif">
					</div>
				</div>

				<div class="form-group">
					<label for="tarea_cplan" class="col-sm-2 control-label">Prod. Min</label>
					<div class="col-sm-10">
						<div class="input-group input-group-find-treetask">
							<input type="text" class="form-control" id="tarea_cplan" name="tarea[cplan]" placeholder="Código" value="@if( $tarea){{ $tarea->cplan }}@endif">
							<input type="text" readonly class="form-control" id="tarea_cplan_mask" name="tarea_cplan_mask" placeholder="Producto Minimo">
							<span class="input-group-addon" data-find-plan data-type-plan="4" data-find-treetask data-input-reference="#tarea_cplan"><i class="fa fa-search"></i></span>
						</div>
					</div>
				</div>

				<div class="form-group text-center">

					<a type="button" class="btn btn-danger" href="{{ URL::route('app_dashboard') }}" data-dismiss="modal">
						<i class="glyphicon glyphicon-remove"></i> Cancelar
					</a>
					@if ( $action == "create" )
						<button type="submit" class="btn btn-success">
							<i class="glyphicon glyphicon-plus"></i>
							Crear
						</button>
					@else
						<button type="submit" class="btn btn-success">
							<i class="glyphicon glyphicon-pencil"></i>
							Editar
						</button>
					@endif

				</div>

			</form>

			<div class="asig_tareas col-md-12 hide">
				<div class="panel panel-default">
					<div class="panel-heading">
						Asociar a Actividades
					</div>

					<div class="panel-body">
						<form action="" id="asignacion" class="form-horizontal">
							<div class="form-group">
								<label for="cactividad" class="col-sm-2 control-label">Actividad</label>
								<div class="col-sm-10">
									<select-activity required="required" name="tareasusuarios[cactividad]" id="cactividad" :activities="activities" @mounted="getActivities" />
								</div>
							</div>
							<div class="form-group">
								<label for="cactividad" class="col-sm-2 control-label">Responsable</label>
								<div class="col-sm-10">
									<select  name="tareasusuarios[user]" id="respo" required class="form-control selectFind">
										<option value="">Responsable</option>
										@foreach ($usuarios as $usuario)
											<option value = "{{$usuario->id}}">{{$usuario->persona->nombres}} {{$usuario->persona->apellidos}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="cactividad" class="col-sm-2 control-label">Responsabilidad</label>
								<div class="col-sm-10">
									<select  name="tareasusuarios[ctirelacion]" id="tirespo" required class="form-control" >
										<option value="">Tipo de responsabilidad</option>
										@foreach ($relaciones as $relacion)
											<option value = "{{$relacion->ctirelacion}}">{{$relacion->ntirelacion}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
        </div>

@endsection

@section('scripts')
<script>

	if ( CRUD_ACTION == "create" ){
		$(".asig_tareas").addClass("hide")
	}

	$("#crear_tarea").submit(function(event){
		var that = this
		event.preventDefault()
		$.ajax({
			url:"{{ $ajax['url'] }}",
			type:"{{ $ajax['method'] }}",
			data: serializeForm(that),
			cache:false,
			contentType: false,
			processData: false,
			success:callbackSuccessAjax,
			error:callbackErrorAjax
		})
	})
</script>
@endsection
