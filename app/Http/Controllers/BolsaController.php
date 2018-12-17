<?php

namespace App\Http\Controllers;

use App\Bolsa;
use App\BolsaMatricula;
use Illuminate\Http\Request;


class BolsaController extends Controller
{   
    const max_matriculas = 2;
    public function listar(Request $r = Request){
        if($r->codigo)
             $bolsas = Bolsa::where('id',$r->codigo)->paginate(10);
        else
            $bolsas = Bolsa::orderByDesc('created_at')->paginate(10);

        return view('juridico.bolsas.index',compact('bolsas'));
    }
    
    public function alterarStatus($status,$itens){
        $bolsas=explode(',',$itens);
        foreach($bolsas as $bolsa){
            if(is_numeric($bolsa)){
                $bolsa = Bolsa::find($bolsa);
                if($bolsa){
                    // Aqui virá uma verificação que se há matrículas antes de excluir
                    switch($status){
                        case 'aprovar':
                            $bolsa->status = 'ativa';
                            $bolsa->save();
                            $atendimento = AtendimentoController::novoAtendimento('Bolsa '.$bolsa->id.' aprovada.',$bolsa->pessoa);
                            break;
                        case 'negar':
                            $bolsa->status = 'indefirida';
                            $bolsa->save();
                            $atendimento = AtendimentoController::novoAtendimento('Bolsa '.$bolsa->id.' indefirida.',$bolsa->pessoa);
                            break;
                        case 'analisando':
                            $bolsa->status = 'analisando';
                            $bolsa->save();
                            $atendimento = AtendimentoController::novoAtendimento('Bolsa '.$bolsa->id.' em análise.',$bolsa->pessoa);
                            break;
                        case 'cancelar':
                            $bolsa->status = 'cancelada';
                            $bolsa->save();
                            $atendimento = AtendimentoController::novoAtendimento('Bolsa '.$bolsa->id.' cancelada.',$bolsa->pessoa);
                            break;
                        case 'apagar':
                            $atendimento = AtendimentoController::novoAtendimento('Solicitação de bolsa '.$bolsa->id.' excluída.',$bolsa->pessoa);
                            $bolsa->delete();
                            break;
                        
                    }
                }
            }
        }
        return redirect()->back()->withErrors(['Bolsa(s) processadas.']);


    }



    /**
     * Gerador de Bolsas, para migração do sistema de bolsas em matriculas para bolsas independentes
     * @return [type] [description]
     */
    public function gerador(){
        //pegar todas matriculas com bolsa
        $matriculas = \App\Matricula::select(['id','desconto','curso','pessoa'])->whereIn('status',['ativa','pendente'])->where('desconto','>',0)->get();

       

        //Contador
       $bolsas_criadas = 0;

        foreach($matriculas as $matricula){

            //verifica se já tem bolsa para essa pessoa nesse curso
            $bolsa = Bolsa::select(['id'])->where('pessoa',$matricula->pessoa)->where('curso',$matricula->curso)->get();
            $inscricao = \App\Inscricao::select(['id','turma'])->where('matricula',$matricula->id)->first();
            //dd($inscricao);



            //se não tiver, criar uma nova
            if(count($bolsa)==0)
            {

                $bolsa = new Bolsa;
                $bolsa->pessoa = $matricula->pessoa;
                $bolsa->curso = $matricula->curso;
                $bolsa->desconto = $matricula->desconto;
                $bolsa->curso = $inscricao->turma->curso->id;
                $bolsa->programa = $inscricao->turma->programa->id;
                $bolsa->validade = '2018-12-31';
                $bolsa->obs = 'Bolsa gerada a partir das matrículas do 1º semestre';
                $bolsa->status = 'ativa'; 
                $bolsa->save();
                $bolsas_criadas++;

            }
        }
        return "Foram geradas ".$bolsas_criadas. " bolsas a partir de matrículas do 1º semestre";
        
    }
    public static function verificaBolsa($pessoa,$matricula){
        $bolsa = Bolsa::where('pessoa',$pessoa)->where(function($query) use ($matricula) {
            $query->where('matricula',$matricula)
            ->orWhere('matricula2',$matricula);
        })->where('status','ativa')->first();
        //
        //dd($bolsa);
        if($bolsa)
            return $bolsa->desconto;
        else
            return null;


    }

    /**
     * Formulário de nova Bolsa com listagem das atuais
     * @param  [Int] $pessoa [Id da pessoa]
     * @return [type]         [description]
     */
    public function nova($pessoa){
        $pessoa = \App\Pessoa::find($pessoa);
        $matriculas = \App\Matricula::where('pessoa',$pessoa->id)->whereIn('status',['ativa','pendente','espera'])->get();
        $bolsas = Bolsa::where('pessoa',$pessoa->id)->get();
        return view('pessoa.bolsa.cadastrar',compact('pessoa'))->with('matriculas',$matriculas)->with('bolsas',$bolsas);
    }

    /**
     * Gravação da Bolsa
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function gravar(Request $request){

        //Falta colocar a verificação de qnde de vagas para bolsisas antes de gerar a Bolsa
        //Recomenda-se que a pessoa que for conceder a bolsa, verifique a quantidade de pessoas bolsistas na turma atua

        $this->validate($request,[
            'pessoa' => 'required|integer',
            'classificacao' =>'required'
        ]);

        //$matricula = \App\Matricula::find($request->matricula);
        //$curso = \App\Curso::find($matricula->curso);

        //verifica se 
        if(count($request->matricula) > self::max_matriculas)
            return redirect()->back()->withErrors(['Mais de duas matrículas selecionadas.']);

        $bolsa = Bolsa::where('pessoa',$request->pessoa)->where('status','ativa')->first();

        if($bolsa){
            $matriculas = \App\BolsaMatricula::where('bolsa',$bolsa->id)->get();
            if(count($matriculas)>= self::max_matriculas)
                return redirect()->back()->withErrors(['Erro: Máximo de matrículas por bolsa já atingido.']);
            else{
                $bolsa_matricula = new BolsaMatricula();
                $bolsa_matricula->bolsa = $bolsa->id;
                $bolsa_matricula->pessoa = $bolsa->pessoa;
                $bolsa_matricula->matricula = $request->matricula[0];
                $bolsa_matricula->programa = ;
                $bolsa_matricula

            }
        }
        else{
            //gerar bolsa
        }




        switch($request->classificacao){
            case 'fesc':
            $desconto = 5;
            break;
            //funcionarios:
            case 'prefeitura':
            $desconto = 3;
            break;
            //encaminhados:
            case 'pmsc':
            $desconto = 7;
            break;
            case 'saude':
            $desconto = 7;
            break;
            case 'caps':
            $desconto = 7;
            break;
            case 'cidadania':
            $desconto = 7;
            break;
            case 'nis':
            $desconto = 6;
            break;
            case 'socioeconomica':
            $desconto = 1;
            break;

        }
        if($this->vericaSeSolicitado($request->pessoa,$request->matricula))
            return redirect()->back()->withErrors(['Bolsa já solicitada.']);



        $bolsa = new Bolsa;
        $bolsa->pessoa = $request->pessoa;
        $bolsa->tipo = $request->classificacao;
        $bolsa->desconto = $desconto;
        $bolsa->status = 'analisando';

        foreach($request->matricula as $matricula){


            
        }

        //dd($bolsa);
        if(date('m')>11)
            $validade = date('Y-12-31 23:23:59', strtotime("+12 months",strtotime(date('Y-m-d')))); 
        else
            $validade = date('Y-12-31 23:23:59'); 

        

        
        $bolsa->save();

        $atendimento = AtendimentoController::novoAtendimento('Solicitação de Bolsa',$request->pessoa);

        return redirect()->back()->withErrors(['Bolsa registrada com sucesso. Aguarde análise da Comissão de Avaliação']);

    }
    public function vericaSeSolicitado($pessoa,$matricula){
        $bolsa = Bolsa::where('pessoa',$pessoa)->where('matricula',$matricula)->get();

        //dd(is_array($bolsa));
        if(count($bolsa))
            return true;
        else
            return false;

    }
    public function imprimir($bolsa){
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');

        $bolsa = Bolsa::find($bolsa);

        if(is_null($bolsa))
            return redirect()->back()->withErrors("Erro ao localizar bolsa");

        $pessoa = \App\Pessoa::find($bolsa->pessoa);

        $pessoa = PessoaController::formataParaMostrar($pessoa);

        $hoje = strftime('%d de %B de %Y', strtotime('today'));



        return view('juridico.bolsas.requerimento',compact('bolsa'))->with('pessoa',$pessoa)->with('hoje',$hoje);

    }

    public function analisar($bolsas){
        $bolsa_array = explode(",", $bolsas);
        if(count($bolsa_array)==1){
            $bolsa = Bolsa::find($bolsa_array[0]);
            if(!$bolsa){
                return redirect()->back()->withErrors(["Bolsa ".$bolsa. " não encontrada em nosso sistema."]);
            }


        }else 
            $bolsa=null;

        return view('juridico.bolsas.analisar')->with('bolsa',$bolsa)->with('bolsas',$bolsas);
        

    }
    public function gravarAnalise(Request $r){
         $this->validate($r,[
            'parecer' => 'required',
        ]);


        $bolsa_array = explode(",", $r->bolsas);
        foreach($bolsa_array as $bolsa_i){
            $bolsa = Bolsa::find($bolsa_i);
            if($bolsa){
                $bolsa->status = $r->parecer;
                $bolsa->obs = $r->obs."\n"."Atualizado em ".date('d/m/Y').' parecer: '.$r->parecer.' por '.session('nome_usuario');
                $bolsa->save();
            }
        }


        return redirect('/juridico/bolsas/liberacao')->withErrors(['Bolsa(s) alteradas com sucesso.']);
    }


    public function uploadForm($bolsa){
        return view('pessoa.bolsa.upload')->with('bolsa',$bolsa);
    }
    public function uploadExec(Request $r){
        $arquivo = $r->file('arquivo');
            
                if (!empty($arquivo)) {
                    $arquivo->move('documentos/bolsas/requerimentos',$r->matricula.'.pdf');
                }

            return redirect(asset('secretaria/atender'));
    }
    public function uploadParecerForm($bolsa){
        return view('pessoa.bolsa.upload-parecer')->with('bolsa',$bolsa);
    }
    public function uploadParecerExec(Request $r){
        $arquivo = $r->file('arquivo');
            
                if (!empty($arquivo)) {
                    $arquivo->move('documentos/bolsas/pareceres',$r->matricula.'.pdf');
                }

            return redirect(asset('secretaria/atender'));
    }

    public function relatorioFPM(){
        $bolsas = Bolsa::where('desconto','3')->get();
        return view('relatorios.bolsistas')->with('bolsas',$bolsas);
    }

    public function ajusteBolsaSemMatricula(){
        $bolsas = Bolsa::where('matricula',null)->get();
        foreach ($bolsas as $bolsa){
            $matricula = \App\Matricula::where('curso',$bolsa->curso)->where('pessoa',$bolsa->pessoa)->first();
            if($matricula){
                $bolsa->matricula = $matricula->id;
                $bolsa->save();
            }
            
       
        }
        return $bolsas;
    }

    public function novaBolsa(){
        $bolsas = Bolsa::all();
        foreach($bolsas as $bolsa){
            $bolsa_m = new BolsaMatricula();
            $bolsa_m->pessoa = $bolsa->pessoa;
            $bolsa_m->bolsa = $bolsa->id;
            $bolsa_m->matricula = $bolsa->matricula;
            $bolsa->save();
        }
    }


}
