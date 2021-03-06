<div >
    <ul class="nav nav-tabs">
      <li class="active"><a class="px-2" data-toggle="tab" href="#retrasadas">Retrasadas</a></li>
      <li><a class="px-2" data-toggle="tab" href="#pendientes">Pendientes</a></li>
      <li><a class="px-2" data-toggle="tab" href="#realizadas">Realizadas</a></li>
    </ul>

    <div class="tab-content">
      <div id="retrasadas" class="tab-pane fade in active">
        @permission('activities.send_reminders')
            <div class="panel text-center">
                <button class="btn btn-success" onclick="Models.Actividades.sendReminders()">Enviar Recordatorios</button>
            </div>
        @endpermission
        <h3>Retrasadas</h3>
        <table class="actividades_retrasadas table">
            <thead>
                <tr>
                    <th>It</th>
                    <th>Actividad</th>
                    <th>Fecha</th>
                    <th>Dias Retraso</th>
                    <th width="100px"></th>
                    <th width="100px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($actividades["retrasadas"] as $actividad)
                    <tr>
                        <td>{{ $actividad->cactividad }}</td>
                        <td>{{ $actividad->nactividad }}</td>
                        <td> {{ $actividad->fini }} </td>
                        <td>{{ $actividad->dias_retraso }}</td>
                        <td>
                            <a class="btn btn-success btn-block" href="{{ URL::route('realizar_actividad',['cactividad'=>$actividad->cactividad]) }}">Realizar</a>
                        </td>
                        <td>
                            <a class="btn btn-primary btn-block" target="_blank" href="{{ URL::route('GET_detalle_actividad',['cactividad'=>$actividad->cactividad]) }}">Detalle</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      </div>
      <div id="pendientes" class="tab-pane fade">
        @permission('activities.send_reminders')
            <div class="panel text-center">
                <button class="btn btn-success" onclick="Models.Actividades.sendReminders()">Enviar Recordatorios</button>
            </div>
        @endpermission
        <h3>Proximas</h3>
        <table class="actividades_pendientes table">
            <thead>
                <tr>
                    <th>It</th>
                    <th>Actividad</th>
                    <th>Fecha</th>
                    <th>Dias Faltantes</th>
                    <th width="100px"></th>
                    <th width="100px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($actividades["pendientes"] as $actividad)
                    <tr>
                        <td>{{ $actividad->cactividad }}</td>
                        <td>{{ $actividad->nactividad }}</td>
                        <td>{{ $actividad->fini }}</td>
                        <td>{{ $actividad->dias_faltantas }}</td>
                        <td>
                            <a class="btn btn-success btn-block" href="{{ URL::route('realizar_actividad',['cactividad'=>$actividad->cactividad]) }}">Realizar</a>
                        </td>
                        <td>
                            <a class="btn btn-primary btn-block" target="_blank" href="{{ URL::route('GET_detalle_actividad',['cactividad'=>$actividad->cactividad]) }}">Detalle</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      </div>
      <div id="realizadas" class="tab-pane fade">
        <h3>Realizadas</h3>
        <table class="actividades_realizadas table">
            <thead>
                <tr>
                    <th>It</th>
                    <th>Actividad</th>
                    <th>Fecha</th>
                    <th width="100px"></th>
                    <th width="100px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($actividades["realizadas"] as $actividad)
                    <tr>
                        <td>{{ $actividad->cactividad }}</td>
                        <td>{{ $actividad->nactividad }}</td>
                        <td>{{ $actividad->ffin }}</td>
                        <td>
                            <a class="btn btn-primary btn-block" target="_blank" href="{{ URL::route('GET_resumen_actividad',['cactividad'=>$actividad->cactividad]) }}">Resumen</a>
                        </td>
                        <td>
                            <a class="btn btn-primary btn-block" target="_blank" href="{{ URL::route('GET_detalle_actividad',['cactividad'=>$actividad->cactividad]) }}">Detalle</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      </div>
    </div>
</div>
