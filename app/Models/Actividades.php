<?php

namespace asies\Models;
use asies\User;
use \Auth;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
/**
 * @property integer $cactividad
 * @property integer $cacta
 * @property integer $cestado
 * @property integer $ctiactividad
 * @property string $nactividad
 * @property string $descripcion
 * @property string $fini
 * @property string $ffin
 * @property boolean $ifhecha
 * @property boolean $ifacta
 * @property boolean $ifarchivos
 * @property string $created_at
 * @property string $updated_at
 * @property Acta $acta
 * @property Estado $estado
 * @property Tiactividade $tiactividade
 * @property Actividadestarea[] $actividadestareas
 * @property Asignaciontarea[] $asignaciontareas
 * @property Evidencias[] $evidencias
 */
class Actividades extends Model
{
	protected $primaryKey = "cactividad";
	/**
	 * @var array
	 */
	protected $fillable = ['cacta', 'cestado', 'ctiactividad', 'nactividad', 'descripcion', 'fini', 'ffin', 'ifhecha', 'ifacta', 'ifarchivos', 'created_at', 'updated_at'];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function acta(){
		return $this->belongsTo('asies\Models\Actas', 'cacta', 'idacta');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function estado(){
		return $this->belongsTo('asies\Models\Estado', 'cestado', 'cestados');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function tiactividades(){
		return $this->belongsTo('asies\Models\Tiactividades', 'ctiactividad', 'ctiactividad');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function actividadestareas(){
		return $this->hasMany('asies\Models\Actividadestarea', 'cactividad', 'cactividad');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function asignaciontareas(){
		return $this->hasMany('asies\Models\Asignaciontarea', 'cactividad', 'cactividad');
	}

	public function evidencias(){
		return $this->hasMany('asies\Models\Evidencias', 'cactividad', 'cactividad');
	}

	public function add_task_user($data,$cactividad=null){
		$user = Auth::user();
		$actividad = $this;
		if ( $cactividad ){
			$actividad = Tareas::where('cactividad', $cactividad)->first();
		}
		$dataCreataion = array('cactividad' => $actividad->cactividad , 'ctirelacion'=>$data["ctirelacion"],'ctarea'=>$data["ctarea"],'user'=>$data["user"]);
		if( AsignacionTareas::where(array('cactividad' => $actividad->cactividad ,'ctarea'=>$data["ctarea"],'user'=>$data["user"]))->exists() ){
			AsignacionTareas::where(array('cactividad' => $actividad->cactividad ,'ctarea'=>$data["ctarea"],'user'=>$data["user"]))->update(['ctirelacion'=>$data["ctirelacion"]]);
			$obj = AsignacionTareas::where(array('cactividad' => $actividad->cactividad ,'ctarea'=>$data["ctarea"],'user'=>$data["user"]))->first();
			Log::info('responsable editado en tarea,',['user (edito)' => $user->id, '' => $actividad->cactividad ,'tarea' => $data['ctarea'] , 'user (responsable)' =>$data["user"]]);
			$data = array("message"=>"Se edito la <b>Responsabilidad</b> del Usuario.");
		}else{
			$obj = AsignacionTareas::create($dataCreataion);
			Log::info('responsable creado en tarea,',['user (edito)' => $user->id, 'actividad' => $obj->cactividad ,'tarea' => $obj->ctarea , 'user (respo)' =>$obj->user]);
			$data = array("message"=>"El usuario se agregro exitosamente");
		}
		return array("obj"=>$obj,"data"=>$data);
	}

	public function remove_task_user($data,$cactividad=null){
		$user = Auth::user();
		$actividad = $this;
		if ( $cactividad ){
			$actividad = Tareas::where('cactividad', $cactividad)->first();
		}

		$dataCreataion = array('cactividad' => $actividad->cactividad , 'ctirelacion'=>$data["ctirelacion"],'ctarea'=>$data["ctarea"],'user'=>$data["user"]);
		if( AsignacionTareas::where(array('cactividad' => $actividad->cactividad ,'ctarea'=>$data["ctarea"],'user'=>$data["user"]))->exists() ){
			AsignacionTareas::where(array('cactividad' => $actividad->cactividad ,'ctarea'=>$data["ctarea"],'user'=>$data["user"]))->delete();
			Log::info('Asignacion Borrada,',['user (borro)' => $user->id, '' => $actividad->cactividad ,'tarea' => $data['ctarea'] , 'user (responsable)' =>$data["user"]]);
			$data = array("message"=>"Se Borro la <b>Responsabilidad</b> del Usuario.");
		}else{
			$data = array("message"=>"La Asignacion No Existe");
		}
		return array("data"=>$data);
	}

	static function getPendientes($ndias = null){
		$now = Carbon::now();
		$now->hour   = 0;
		$now->minute = 0;
		$now->second = 0;

		$actividades = Actividades::where('ffin', '<', $now)->get();
		$actividades_filtradas = collect();
		foreach ($actividades as $actividad ) {
			$actividad->calcularDias();
			if ( $ndias ){
				if ( $actividad->dias_faltantas <= $ndias ){
					$actividades_filtradas->push($actividad);
				}
			}else{
				$actividades_filtradas->push($actividad);
			}
		}

		return $actividades_filtradas;
	}

	static function sendEmailsReminder($actividad){

		$emails = $actividad->getEmails();
		//$emails = ['sistematizaref.programador5@gmail.com','foo@bar.baz'];

		$data = array(
			'actividad' => $actividad,
			'emails' => $emails,
		);

		$status = \Mail::send('emails.reminderActivity', $data, function ($message) use ($data){
			$message->to($data["emails"])->subject('Recordatorio de Actividades');
		});

		return [
			"status" => $status,
			"emails" => $emails,
			"actividad" => $actividad->toArray(),
			"failures" => \Mail::failures(),
		];
	}

	static function getGrouped(){
		$actividades = array(
			"realizadas"=>collect(),
			"retrasadas"=>collect(),
			"pendientes"=>collect(),
		);
		$actividades["realizadas"] = Actividades::where("ifhecha",1)->get();

		$resto_actividades = Actividades::where("ifhecha",0)->get();

		foreach ($resto_actividades as $actividad) {
			$actividad->calcularDias();

			if ( $actividad->dias_faltantas ){
				$actividades["pendientes"]->push($actividad);
			}elseif( $actividad->dias_retraso ){
				$actividades["retrasadas"]->push($actividad);
			}
		}

		/* No funciona el ordenamiento */
		$actividades["pendientes"] = $actividades["pendientes"]->sort(function ($a, $b) {
			if($a->dias_faltantas == $b->dias_faltantas){ return 0 ; }
			return ($a->dias_faltantas < $b->dias_faltantas) ? -1 : 1;
		});

		$actividades["pendientes"]->values()->all();

		$actividades["retrasadas"] = $actividades["retrasadas"]->sort(function ($a, $b) {
			if($a->dias_retraso == $b->dias_retraso){ return 0 ; }
			return ($a->dias_retraso < $b->dias_retraso) ? -1 : 1;
		});
		$actividades["retrasadas"]->values()->all();

		return $actividades;
	}

	public function updateState(){
		$response = array( "message" => "", "ifhecha" => null );

		/*
		$tareas_no_hecha = \DB::table('asignaciontareas')
			->join('actividades', 'asignaciontareas.cactividad', '=', 'actividades.cactividad')
			->join('tareas', 'asignaciontareas.ctarea', '=', 'tareas.ctarea')

			->select('asignaciontareas.*')

			->where('asignaciontareas.cactividad', $this->cactividad)
			->where('asignaciontareas.ifhecha', 0)

			->groupBy('asignaciontareas.ctarea')

			->get();
		*/
		$tareas_no_hecha = AsignacionTareas::where("ifhecha",0)->where("cactividad",$this->cactividad)->first();
		$ifhecha = null;

		$ifhecha = 0;
		if ( ! $tareas_no_hecha ){
			if ( $this->ifacta ){
				if ( $this->cacta ){
					$ifhecha = 1;
					$response["message"] = "La Actividad se completo";
				}else{
					$response["message"] = "Las Tareas se completaron pero no se ha creado acta";
					$ifhecha = 0;
				}
			}else{
				$ifhecha = 1;
				$response["message"] = "La Actividad se completo";
			}
			$checklist = $this->checklist();
			if ( $checklist ){
				if ( $checklist->ifhecha ){
					$ifhecha = 1;
					$response["message"] = "La Actividad se completo";
				}else{
					$response["message"] = "Las Tareas se completaron pero no se ha completado el checklist";
					$ifhecha = 0;
				}
			}else{
				$ifhecha = 1;
				$response["message"] = "La Actividad se completo";
			}
			if ( count($this->evidencias) > 0 ){
				$ifhecha = 1;
				$response["message"] = "La Actividad se completo";
			}else{
				$response["message"] = "Las Tareas se completaron pero no se ha creado evidencias";
				$ifhecha = 0;
			}

		}else{
			$ifhecha = 0;
		}
		Actividades::where('cactividad', $this->cactividad)->update(['ifhecha' => $ifhecha]);
		$response["ifhecha"] = $ifhecha;

		return $response;
	}

	public function getStateTareas(){
		$tareas = $this->getTareas();
		$state = [
			"ok" => 0,
			"not_ok" => 0,
			"total" => count($tareas),
		];
		foreach ($tareas as $tarea) {
			if ( $tarea->ifhecha == "1" ){
				$state["ok"] += 1;
			}else{
				$state["not_ok"] += 1;
			}
		}
		return $state;
	}
	public function getTareas($iduser=null){
		if ( $iduser ){
			$tareas = \DB::table('asignaciontareas')
				->join('users', 'asignaciontareas.user', '=', 'users.id')
				->join('tareas', 'asignaciontareas.ctarea', '=', 'tareas.ctarea')
				->select('tareas.*','asignaciontareas.ifhecha','asignaciontareas.valor_tarea')
				->where('asignaciontareas.cactividad', $this->cactividad)
				->where('users.id', $iduser)
				->groupBy('ctarea')
				->get();
		}else{
			$tareas = \DB::table('asignaciontareas')
				->join('tareas', 'asignaciontareas.ctarea', '=', 'tareas.ctarea')
				->select('tareas.*','asignaciontareas.ifhecha','asignaciontareas.valor_tarea')
				->where('asignaciontareas.cactividad', $this->cactividad)
				->groupBy('ctarea')
				->get();
		}
		return $tareas;
	}

	public function getAsignacion(){
		$asignaciones = \DB::table('asignaciontareas')
			->join('users', 'asignaciontareas.user', '=', 'users.id')
			->join('tareas', 'asignaciontareas.ctarea', '=', 'tareas.ctarea')
			->select('asignaciontareas.*')
			->where('asignaciontareas.cactividad', $this->cactividad)
			->get();
			foreach ($asignaciones as $asignacion) {
				$asignacion->tarea = Tareas::where('ctarea',$asignacion->ctarea)->first();
				$asignacion->actividad =Actividades::where('cactividad',$asignacion->cactividad)->first();
				$asignacion->relacion = TiRelaciones::where('ctirelacion',$asignacion->ctirelacion)->first();
				$asignacion->usuario = User::where('id',$asignacion->user)->first();
			}
		return $asignaciones;
	}

	public function calcularDias(){
		$now = Carbon::now();
		$now->hour   = 0;
		$now->minute = 0;
		$now->second = 0;

		$this->dias_faltantas = 0;
		$this->dias_retraso = 0;
		$factividad = Carbon::parse($this->ffin);

		if( $factividad > $now ) {
			$this->dias_faltantas = $factividad->diffInDays($now);
		}else{
			$this->dias_retraso = $factividad->diffInDays($now);
		}
	}

	public function generarEstado(){
		$this->calcularDias();
		if( $this->dias_faltantas != 0 ) {
			$this->state = "to_do";
		}else if( $this->dias_retraso != 0){
			$this->state = "pending";
		}
	}

	public function getEmails(){
		$emails = array();
		$asignaciones = $this->getAsignacion();
		foreach ($asignaciones as $asignacion) {
			array_push($emails, $asignacion->usuario->email);
		}
		$emails = array_unique($emails);
		return $emails;
	}

	public function getEvidencias($only_count=false){
		$evidencias = Evidencias::where('cactividad', $this->cactividad)->get();

		if ( $only_count ){
			return count($evidencias);
		}else{
			return $evidencias;
		}
	}
	public function getChecklist(){

		$checklist = null;
		$cactividad = $this->cactividad;

		$checklist = Checklists::where("cactividad",$cactividad)->first();
		if ( $checklist ){
			$cpreguntas = ChecklistPreguntas::select('cpregunta')->where("cchecklist",$checklist->cchecklist)->orderBy("orden")->get();
			$checklist->preguntas = Preguntas::whereIn('cpregunta', $cpreguntas)->orderBy("ctipregunta")->get();
			$checklist->cantidad_preguntas = count($checklist->preguntas);
			foreach ($checklist->preguntas as $pregunta) {
				$copciones = OpcionesPregunta::select("copcion")->where("ctipregunta",$pregunta->ctipregunta)->get();
				$pregunta->opciones = Opciones::whereIn("copcion",$copciones)->get();
				$pregunta->respuesta = ChecklistDeta::where("cchecklist",$checklist->cchecklist)->where("cpregunta",$pregunta->cpregunta)->first();

				$pregunta->evidencias = [];
				$pregunta->cantidad_evidencias = 0;
				if ( $pregunta->respuesta ){
					$pregunta->evidencias = ChecklistEvidencias::where("cchecklistdeta",$pregunta->respuesta->id)->get();
					$pregunta->cantidad_evidencias = count($pregunta->evidencias);
				}
			}
			$checklist = $checklist;

			$tipreguntas = TiPreguntas::all();
			$estadisticas = [];
			foreach ($tipreguntas as $tipregunta) {
				$cpreguntas = ChecklistPreguntas::select('cpregunta')->where("cchecklist",$checklist->cchecklist)->orderBy("orden")->get();
				$total_preguntas = Preguntas::whereIn('cpregunta', $cpreguntas)->where("ctipregunta",$tipregunta->ctipregunta)->count();

				$opcionespregunta = OpcionesPregunta::where("ctipregunta",$tipregunta->ctipregunta)->get();

				$data = [];

				$data["total"] = $total_preguntas;
				$data["tipregunta"] = $tipregunta;

				$opciones = [];
				foreach ($opcionespregunta as $opcion) {
					$data2 = [
						"opcion" => $opcion->opcion,
						"cantidad" => ChecklistDeta::where("cchecklist",$checklist->cchecklist)->where("copcion",$opcion->copcion)->count(),
					];
					array_push($opciones, $data2);
				}

				$data["opciones"] = $opciones;
				array_push($estadisticas, $data);
			}
			$checklist->estadisticas = $estadisticas;
		}
		return $checklist;
	}
	public function checklist(){
		$cactividad = $this->cactividad;
		$checklist = Checklists::where("cactividad",$cactividad)->first();
		return $checklist;
	}
}
