<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscricao extends Model
{
	use SoftDeletes;
	protected $table  = 'inscricoes';
    //
    public function getPessoaAttribute($value){
		$pessoa=Pessoa::where('id',$value)->get(['id','nome'])->first();
		return $pessoa;
	}

	public function getTurmaAttribute($value){
		$curso=Turma::where('id',$value)->get(['id','programa','carga','curso','disciplina','professor','local','dias_semana','hora_inicio','hora_termino','data_inicio','data_termino','status'])->first();
		return $curso;
	}
	public function setTurma($value){
		$this->turma = $value;
		return $this->turma;

	}
	public function getCurso(){
		$curso=Turma::where('id',$value)->get(['id','curso']);
		return $curso->curso;

	}

}
