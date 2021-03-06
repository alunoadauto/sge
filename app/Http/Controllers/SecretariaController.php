<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pessoa;
use App\Atendimento;
use App\Matricula;
use App\Boleto;
use App\Lancamento;
use App\Inscricao;
use Session;
use App\classes\Strings;

class SecretariaController extends Controller
{
    //
    public function iniciarAtendimento()
	{
		return view('secretaria.inicio-atendimento');
	}
	public function atender($id=0){

		if($id>0){
			session('pessoa_atendimento',$id);
		}
		else{
			$id=session('pessoa_atendimento');
		}
		
		$pessoa=Pessoa::find($id);
		// Verifica se a pessoa existe
		if(!$pessoa)
			return redirect(asset('/secretaria/pre-atendimento'));
		else
			Session::put('pessoa_atendimento',$id);
		
		$pessoa=PessoaController::formataParaMostrar($pessoa);
		if(isset($pessoa->telefone))
			$pessoa->telefone=Strings::formataTelefone($pessoa->telefone);
		if(isset($pessoa->telefone_alternativo))
			$pessoa->telefone_alternativo=Strings::formataTelefone($pessoa->telefone_alternativo);
		if(isset($pessoa->telefone_contato))
			$pessoa->telefone_contato=Strings::formataTelefone($pessoa->telefone_contato);

		//inicia atendimento
		
		if(!Session::get('atendimento')){
			
			$atendimento=new Atendimento();
			$atendimento->atendente=Session::get('usuario');
			$atendimento->usuario=$pessoa->id;
			$atendimento->save();
			
			Session::put('atendimento', $atendimento->id);
			
			
		}
		$errosMsg=\App\PessoaDadosGerais::where('pessoa',$id)->where('dado',20)->get();
		//listar todas matriculas
		//if Mostrar estiver definido, serão exibidos todos os dados
		if(isset($_GET["mostrar"])){
			 $matriculas=Matricula::where('pessoa', Session::get('pessoa_atendimento'))->orderBy('id','desc')->limit(20)->get();
			 //listar inscrições de cada matricula;
			 foreach($matriculas as $matricula){
			 	$matricula->getInscricoes();
			 }

			 $inscricoes = Inscricao::where('pessoa',$id)->where('matricula',null)->get();

			 //listar Boletos
			 $boletos = Boleto::where('pessoa',$id)->orderBy('id','desc')->limit(20)->get();
			 //listar lancamentos de cada boleto;
			 foreach($boletos as $boleto){
			 	$boleto->getLancamentos();
			 }
			
			 //Listar lançamentos
		 	$lancamentos = Lancamento::where('pessoa',$id)->where('boleto',null)->get();
		 	$atividades_aquaticas = $matriculas->whereIn('status',['ativa','pendente'])->whereIn('curso',['898','1151','1152']);
		 	
		 	
			
		}
		// mostrar somente dados ativos/ok
		else{
			 $matriculas=Matricula::where('pessoa', Session::get('pessoa_atendimento'))
			 	->whereIn('status',['ativa','pendente','espera'])
			 	/*
			 	->where(function($query){ $query
							->where('status','ativa')
							->orwhere('status', 'pendente');
					})*/
			 	->orderBy('id','desc')->get();
			 //listar inscrições de cada matricula;
			 foreach($matriculas as $matricula){
			 	$matricula->getInscricoes();

			 }

			 $inscricoes = Inscricao::where('pessoa',$id)
			 	->where('matricula',null)
			 	->where(function($query){ $query
							->where('status','regular')
							->orwhere('status', 'pendente');
					})
			 	->get();

			 //dd($inscricoes);

			 //listar Boletos
			 $boletos = Boleto::where('pessoa',$id)
			 	->where(function($query){ $query
							->where('status','gravado')
							->orwhere('status', 'impresso')
							->orwhere('status', 'emitido');
					})
			 	->orderBy('id','desc')
			 	->get();
			 //listar lancamentos de cada boleto;
			 foreach($boletos as $boleto){
			 	$boleto->getLancamentos();
			 }
			
			 //Listar lançamentos
			 $lancamentos = Lancamento::where('pessoa',$id)->where('boleto',null)->where('status',null)->get();
			 $atividades_aquaticas = $matriculas->WhereIn('curso',['898','1151','1152']);


		}
		//return $matriculas;
		$atestado = \App\Atestado::where('pessoa',$id)->first();
		if($atestado){
			if(isset($atividades_aquaticas))
				$atestado->validade = $atestado->calcularVencimento(12);
			else
				$atestado->validade = $atestado->calcularVencimento(3);

		}

		


		return view('secretaria.atendimento', compact('pessoa'))->with('matriculas',$matriculas)->with('boletos',$boletos)->with('lancamentos',$lancamentos)->with('inscricoes',$inscricoes)->with('errosPessoa',$errosMsg)->with('atestado',$atestado);
	}
}
