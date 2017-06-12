<div class="row">
    <ul class="nav nav-tabs">
      <li><a data-toggle="tab" href="#retrasadas">Retrasadas</a></li>
      <li><a data-toggle="tab" href="#pendientes">Pendientes</a></li>
      <li><a data-toggle="tab" href="#realizadas">Realizadas</a></li>
    </ul>

    <div class="tab-content">
      <div id="retrasadas" class="tab-pane fade in active">
        @permission('activities.send_reminders')
            <div class="panel">
                <button class="btn btn-success" onclick="Models.Actividades.sendReminders()">Enviar Recordatorios</button>
            </div>
        @endpermission
        <h3>Retrasadas</h3>
        <table class="actividades_retrasadas table">
            <thead>
                <tr>
                    <th>Actividad</th>
                    <th>Fecha</th>
                    <th>Dias Retraso</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($actividades["retrasadas"] as $actividad)
                    <tr>
                        <td>{{ $actividad->nactividad }}</td>
                        <td>{{ date('Y-m-d',strtotime($actividad->ffin)) }}</td>
                        <td>{{ $actividad->dias_retraso }}</td>
                        <td>
                            <a class="btn btn-primary" target="_blank" href="{{ URL::route('GET_detalle_actividad',['cactividad'=>$actividad->cactividad]) }}">Detalle</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      </div>
      <div id="pendientes" class="tab-pane fade">
        <h3>Pendientes</h3>
        <table class="actividades_pendientes table">
            <thead>
                <tr>
                    <th>Actividad</th>
                    <th>Fecha</th>
                    <th>Dias Faltantes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($actividades["pendientes"] as $actividad)
                    <tr>
                        <td>{{ $actividad->nactividad }}</td>
                        <td>{{ date('Y-m-d',strtotime($actividad->ffin)) }}</td>
                        <td>{{ $actividad->dias_faltantas }}</td>
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
                    <th>Actividad</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($actividades["realizadas"] as $actividad)
                    <tr>
                        <td>{{ $actividad->nactividad }}</td>
                        <td>{{ date('Y-m-d',strtotime($actividad->ffin)) }}</td>
                        <td>
                            <a class="btn btn-primary" target="_blank" href="{{ URL::route('GET_resumen_actividad',['cactividad'=>$actividad->cactividad]) }}">Resumen</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      </div>
    </div>
</div>