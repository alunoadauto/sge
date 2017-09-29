<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{

	protected $appends=['texto_status','icone_status'];

	public function setDiasSemanaAttribute($value){
		$this->attributes['dias_semana'] = implode(',',$value);
	}
	public function getDiasSemanaAttribute($value){
		return explode(',',$value);
	}
	public function setValorAttribute($value){
		$this->attributes['valor'] = str_replace(',', '.', $value);
	}
	public function getValorAttribute($value){
		return number_format($value,2,',','.');
	}
	public function setAtributosAttribute($value){
		if(count($value))
			$this->attributes['atributos'] = implode(',',$value);	
	}
	public function getAtributosAttribute($value){
		return explode(',',$value);
	}
	public function getProfessorAttribute($value){
		$professor=Pessoa::where('id',$value)->get(['id','nome'])->first();
		return $professor;
	}
	public function getDataInicioAttribute($value){
		return Carbon::parse($value)->format('d/m/Y');
	}
	public function getDataTerminoAttribute($value){
		return Carbon::parse($value)->format('d/m/Y');
	}
	public function getProgramaAttribute($value){
		return Programa::find($value);
	}
	public function getCursoAttribute($value){
		$curso=Curso::where('id',$value)->get(['id','nome','carga'])->first();
		return $curso;
	}
	public function getLocalAttribute($value){	
		return Local::find($value);
	}
	public function getHoraInicioAttribute($value){	
		return substr($value,0,5);
	}
	public function getHoraTerminoAttribute($value){	
		return substr($value,0,5);
	}
	public function getTextoStatusAttribute($value){
		switch($this->attributes['status']){
			case 0:
				return "Encerrada";
				break;
			case 1:
				return "Aguardando abrir matrícula";
				break;
			case 2:
				return "Matrículas abertas";
				break;
			case 4:
				return "Em andamento, aberta";
				break;
			case 5: 
				return "Em andamento";
				break;
			case 6: 
				return "Turma Completa";
				break;
			default:
				return "Indefinida";
				break;
		}//end switch
	}
	public function getIconeStatusAttribute($value){
		switch($this->attributes['status']){
			case 0:
				return "ban";
				break;
			case 1:
				return "clock-o";
				break;
			case 2:
				return "circle-o";
				break;
			case 4:
				return "check-circle-o";
				break;
			case 5: 
				return "check-circle";
				break;
			case 6: 
				return "circle";
				break;
			default:
				return "question-circle";
				break;
		}//end switch
	}


    



}