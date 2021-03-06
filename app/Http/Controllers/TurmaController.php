<?php

namespace App\Http\Controllers;

use App\Turma;
use App\Local;
use App\Programa;
use App\classes\Data;
use App\PessoaDadosAdministrativos;
use App\Parceria;
use App\Pessoa;
use App\Inscricao;
use App\CursoRequisito;
//use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;

class TurmaController extends Controller
{
    /**
     * Listagem de turmas para o setor pedagógico.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $turmas = $this->listagemGlobal($request->filtro,$request->valor,$request->removefiltro,$request->remove);

        $programas=Programa::all();
        $professores = PessoaDadosAdministrativos::getFuncionarios('Educador');
        $professores = $professores->sortBy('nome_simples');
        $locais = Local::select(['id','sigla','nome'])->orderBy('sigla')->get();
        

        //return $professores;
        //return $turmas;
        //$dados=['alert_sucess'=>['hello world']];
        return view('pedagogico.turma.listar', compact('turmas'))->with('programas',$programas)->with('professores', $professores)->with('locais',$locais)->with('filtros',$_SESSION['filtro_turmas']);
    }





    /**
     * Pedagogico ver dados das turmas.
     * @param  [type] $turma [description]
     * @return [type]        [description]
     */
    public function mostrarTurma($turma){
        $turma=Turma::find($turma);
        if (empty($turma))
            return redirect(asset('/secretaria/turmas'));
        $inscricoes=Inscricao::where('turma','=', $turma->id)->where('status','<>','cancelada')->get();
        $inscricoes->sortBy('pessoa.nome');
        $requisitos = CursoRequisito::where('para_tipo','turma')->where('curso',$turma->id)->get();
        //return $inscricoes;
        return view('pedagogico.turma.mostrar-dados',compact('turma'))->with('inscricoes',$inscricoes)->with('requisitos',$requisitos);


    }


    /**
     * Listador global de turmas
     * Suporta os seguintes tipos de filtros:
     *     -programa
     *     -professor
     *     -local
     *     -status
     * 
     * @param  [type]  $filtro [description]
     * @param  [type]  $valor  [description]
     * @param  integer $remove [description]
     * @param  integer $ipp    [Quantidade de itens por página]
     * @return [type]          [description]
     */
    public function listagemGlobal($filtro=null,$valor=null,$rem_filtro=null,$remove=0,$ipp=50){

        session_start();
 
        
        if(isset($_SESSION['filtro_turmas']))
        {
            $filtros = $_SESSION['filtro_turmas'];
            
        }
        else
        {
            $filtros = array();
            
        }

        //verifica se tem algum filtro na url
        if(isset($filtro) && isset($valor)){
           
            //verifica se ja existe esse filtro
            if(array_key_exists($filtro, $filtros)){

                //verifica se o valor ja consta no array, se constar retorna a chave
                $busca = array_search($valor, $filtros[$filtro]);


                //se nao tiver
                if($busca === false){

                    //grava novo valor
                    $filtros[$filtro][] = $valor;
                }
                //se ja tiver
                else
                {
                    //verifica se foi passado por url pra apagar esse filtro
                    if($remove > 0){

                        //apaga a chave com o filtro
                        unset($filtros[$filtro][$busca]);

                    }
                }
            }
            else{
                //die('chave nao existe');
                $filtros[$filtro][] = $valor;
            }
            

        }
        if($rem_filtro != null){
            if(isset($filtros[$rem_filtro]))
                unset($filtros[$rem_filtro]);
        }
        

        $_SESSION['filtro_turmas'] = $filtros;

        $turmas=Turma::select('*', 'turmas.id as id' ,'turmas.vagas as vagas','turmas.carga as carga','turmas.programa as programa','disciplinas.id as disciplinaid','cursos.id as cursoid','turmas.programa as programaid','turmas.valor as valor')
                ->join('cursos', 'turmas.curso','=','cursos.id')
                ->leftjoin('disciplinas', 'turmas.disciplina','=','disciplinas.id');

        if(isset($filtros['programa']) && count($filtros['programa'])){
            $turmas = $turmas->whereIn('turmas.programa', $filtros['programa']); 
        }

        if(isset($filtros['professor']) && count($filtros['professor'])){
            $turmas = $turmas->whereIn('turmas.professor', $filtros['professor']); 
        }
        if(isset($filtros['local']) && count($filtros['local'])){
            $turmas = $turmas->whereIn('turmas.local', $filtros['local']); 
        }

        if(isset($filtros['status']) && count($filtros['status'])){
            $turmas = $turmas->whereIn('turmas.status', $filtros['status']); 
        }

        $turmas = $turmas->orderBy('cursos.nome')->orderBy('disciplinas.nome');
        /*

        foreach($filtros as $coluna=>$filtro){
            if(count($filtro)>0){
                    $turmas = $turmas->where($coluna, $filtro);                
            }
        }
        
        if(isset($r->data)){
            $termino = date('Y-m-d', strtotime("+5 months",strtotime($r->data))); 
            
            $turmas=Turma::orderBy('curso')->where('data_inicio','>=',$r->data)->where('data_termino','<=',$termino)->get(); 
            //return $turmas;
        }
        else{
            $turmas=Turma::orderBy('curso')->paginate(50);
              //return $turmas;

        }
        if(isset($r->programa)){
            $turmas=Turma::orderBy('curso')->where('programa',$r->programa)->paginate(50);

        }
        */

        $turmas = $turmas->paginate($ipp);

        return $turmas;

    }




    /**
     * Listagem de turmas da secretaria
     * 
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function listarSecretaria(Request $request)
    {
        
        $turmas = $this->listagemGlobal($request->filtro,$request->valor,$request->removefiltro,$request->remove);
        $programas=Programa::all();
        $professores = PessoaDadosAdministrativos::getFuncionarios('Educador');
        $professores = $professores->sortBy('nome_simples');
        $locais = Local::select(['id','sigla','nome'])->orderBy('sigla')->get();

        return view('secretaria.listar-turmas', compact('turmas'))->with('programas',$programas)->with('professores', $professores)->with('locais',$locais)->with('filtros',$_SESSION['filtro_turmas']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $programas=Programa::get();
        $requisitos=RequisitosController::listar();
        //$cursos=Curso::getCursosPrograma(); ok
        $professores=PessoaDadosAdministrativos::getFuncionarios('Educador');
        $unidades=Local::get(['id' ,'nome']);
        $parcerias=Parceria::orderBy('nome')->get();
        //Locais=Local::getLocaisPorUnidade($unidade);
        $dados=collect();
        $dados->put('programas',$programas);
        $dados->put('professores',$professores);
        $dados->put('unidades',$unidades);
        $dados->put('parcerias',$parcerias);

        //return $dados;

        return view('pedagogico.turma.cadastrar',compact('dados'))->with('requisitos',$requisitos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         //dd($request);
        $this->validate($request, [
            "programa"=>"required|numeric",
            "curso"=>"required|numeric",
            "professor"=>"required|numeric",
            "unidade"=>"required|numeric",
            "dias"=>"required",
            "dt_inicio"=>"date|required",
            "dt_termino"=>"date|required",
            "hr_inicio"=>"required",
            "hr_termino"=>"required",
            "vagas"=>"numeric|required",
            "valor"=>"numeric|required"


        ]);
        //dd($request);



        $turma=new Turma;
        $turma->programa=$request->programa;
        $turma->curso=$request->curso;
        $turma->disciplina=$request->disciplina;
        $turma->professor=$request->professor;
        $turma->local=$request->unidade;
        $turma->dias_semana=$request->dias;
        $turma->data_inicio=$request->dt_inicio;
        $turma->data_termino=$request->dt_termino;
        $turma->hora_inicio=$request->hr_inicio;
        $turma->hora_termino=$request->hr_termino;
        $turma->parceria=$request->parceria;
        $turma->periodicidade=$request->periodicidade;
        $turma->valor=$request->valor;
        $turma->vagas=$request->vagas;
        $turma->carga=$request->carga;
        $turma->atributos=$request->atributo;
        $turma->status='espera';
        //return $turma->dias_semana;



        $turma->save();

        if(isset($request->requisito)){
            foreach($request->requisito as $req){
                $curso_requisito=new CursoRequisito;
                $curso_requisito->para_tipo='turma';
                $curso_requisito->curso=$turma->id;
                $curso_requisito->requisito=$req;
                $curso_requisito->obrigatorio=1;
                $curso_requisito->timestamps=false;
                $curso_requisito->save();
            }
        }
        if($request->btn==1)
            return redirect(asset('/pedagogico/turmas'));
        else
            return redirect(asset('/pedagogico/turmas/cadastrar'));
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Turma  $turma
     * @return \Illuminate\Http\Response
     */
    public function show(Turma $turma)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Turma  $turma
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $turma=Turma::find($id);
        if($turma){
            $programas=Programa::get();
            $parcerias=Parceria::orderBy('nome')->get();
            //$cursos=Curso::getCursosPrograma(); ok
            $professores=PessoaDadosAdministrativos::getFuncionarios('Educador');
            $unidades=Local::get();
            //Locais=Local::getLocaisPorUnidade($unidade);
            $dados=collect();
            $dados->put('programas',$programas);
            $dados->put('professores',$professores);
            $dados->put('unidades',$unidades);
            $dados->put('parcerias', $parcerias);
            $turma->data_iniciov=Data::converteParaBd($turma->data_inicio);
            $turma->data_terminov=Data::converteParaBd($turma->data_termino);

            //return $turma;

            return view('pedagogico.turma.editar',compact('dados'))->with('turma',$turma);
        }
        else
            return $this->index();
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Turma  $turma
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Turma $turma)
    {
        $this->validate($request, [
            "turmaid"=>"required|numeric",
            "programa"=>"required|numeric",
            "curso"=>"required|numeric",
            "professor"=>"required|numeric",
            "unidade"=>"required|numeric",
            "dias"=>"required",
            "dt_inicio"=>"required",
            "hr_inicio"=>"required",
            "vagas"=>"required",
            "valor"=>"required",
            "periodicidade"=>"required"


        ]);
        $turma=Turma::find($request->turmaid);
        $turma->programa=$request->programa;
        $turma->curso=$request->curso;
        $turma->disciplina=$request->disciplina;
        $turma->professor=$request->professor;
        $turma->local=$request->unidade;
        $turma->dias_semana=$request->dias;
        $turma->data_inicio=$request->dt_inicio;
        $turma->data_termino=$request->dt_termino;
        $turma->hora_inicio=$request->hr_inicio;
        $turma->hora_termino=$request->hr_termino;
        //se valor mudar, mudar valor das matriculas
        $turma->valor=$request->valor;
        //----------------------------------------
        $turma->vagas=$request->vagas;
        $turma->carga=$request->carga;
        $turma->atributos=$request->atributo;
        $turma->periodicidade=$request->periodicidade;
        $turma->parceria = $request->parceria;
        $turma->update();
        return redirect(asset('/pedagogico/turmas'));
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Turma  $turma
     * @return \Illuminate\Http\Response
     */
    public function destroy($itens_url)
    {
        $msgs=Array();
        $turmas=explode(',',$itens_url);
        foreach($turmas as $turma){
            if(is_numeric($turma)){
                $turma=Turma::find($turma);
                if($turma){
                    // verificação que se há matrículas antes de cancelar
                    $inscricoes=Inscricao::where('turma',$turma->id)->get();
                    if(count($inscricoes)==0){
                        $msgs[]= "Turma ".$turma->id." excluída com sucesso.";
                        $turma->delete();
                    }
                    else{
                        $msgs[]="Turma ".$turma->id." não pôde ser excluída pois possui alunos inscritos. Caso não apareça, a inscrição pode ter sido cancelada, mesmo assim precisamos preservar o histórico das inscrições.";
                    }
                }
            }
        }
        return redirect()->back()->withErrors($msgs);
    }
    public function status($status,$itens_url)
    {
        $turmas=explode(',',$itens_url);
        foreach($turmas as $turma){
            if(is_numeric($turma)){
                $turma=Turma::find($turma);
                if($turma){
                    // Aqui virá uma verificação que se há matrículas antes de excluir
                    $msgs['alert_sucess'][]="Turma ".$turma->id." modificada com sucesso.";
                    if($status=='encerrada')
                        $this->finalizarTurma($turma);
                    else{
                        $turma->status=$status;
                        $turma->save();
                    }
                }
            }
        }
        //return $msgs;
        return redirect('/secretaria/turmas')->withErrors($msgs);
    }
    public function turmasDisponiveis($turmas_atuais='0',$ordered_by='')
    {
        $turmas_af=collect();// objetos de turmas atuais
        $lista=array();
        $lst=array();

        //lista turmas atuais passados por parametro e as coloco na collection $turmas_af
        $turmas_atuais=explode(',',$turmas_atuais);
        foreach($turmas_atuais as $turma){
            if(is_numeric($turma)){
                if(Turma::find($turma))
                    $turmas_af->push(Turma::find($turma));
            }
        }
        //return $turmas_af;
  

        // se não tiver nenhuma turma atual
        if(count($turmas_af)==0){
            $turmas=Turma::select('*', 'turmas.id as id' ,'turmas.carga as carga','turmas.programa as programa','turmas.vagas as vagas' ,'disciplinas.id as disciplinaid','cursos.id as cursoid')
                -> whereIn('turmas.status', ['inscricao','iniciada'])
                ->join('cursos', 'turmas.curso','=','cursos.id')
                ->leftjoin('disciplinas', 'turmas.disciplina','=','disciplinas.id')
                ->whereNotIn('turmas.id', $lst)
                ->orderBy('cursos.nome')
                ->orderBy('disciplinas.nome')
                ->get();
                //dd($turmas);

            
            //return $turmas;
            $programas=Programa::get();
            //return $turmas;
            return view('secretaria.inscricao.lista-formatada', compact('turmas'))->with('programas',$programas);
        }

        // ja se tem turma selecionada ou atual
        else{

            //Para cada turma 
            foreach($turmas_af as $turma){

                    // tira um minuto da turma atual para não conflitar com as que começam exatamente no  mesmo horário
                    $hora_fim=date("H:i",strtotime($turma->hora_termino." - 1 minutes"));

                    //para cada dia da semana da turma atual
                    foreach($turma->dias_semana as $turm){
                        $data = \Carbon\Carbon::createFromFormat('d/m/Y', $turma->data_termino)->format('Y-m-d');

                        //adiciona turmas que conflitam nessa lista
                        $lista[]=Turma::where('dias_semana', 'like', '%'.$turm.'%')->whereBetween('hora_inicio', [$turma->hora_inicio,$hora_fim])->where('data_inicio','<=',$data)->get(['id']);
                    }
                    
                }
            //transforma resultados em array
            foreach($lista as $col_turma){
                foreach($col_turma as $obj_turma)
                    $lst[]=$obj_turma->id;

            }

            //return $lst;

            // seleciona todas as turmas disponíveis (tira da lista aquelas que conflitam)
            $turmas=Turma::select('*', 'turmas.id as id', 'turmas.carga as carga','turmas.programa as programa','turmas.vagas as vagas' ,'disciplinas.id as disciplinaid','cursos.id as cursoid')
                -> whereIn('turmas.status', ['inscricao','iniciada'])
                ->join('cursos', 'turmas.curso','=','cursos.id')
                ->leftjoin('disciplinas', 'turmas.disciplina','=','disciplinas.id')
                ->whereNotIn('turmas.id', $lst)
                ->orderBy('cursos.nome')
                ->orderBy('disciplinas.nome')
                ->get();
               //dd($turmas);


        }
        $programas=Programa::get();

        //return $turmas;
        return view('secretaria.inscricao.lista-formatada', compact('turmas'))->with('programas',$programas);
        
    }
    public function turmasEscolhidas($lista='0'){
        $turmas=collect();
        $valor=0;
        $uati=0;
        $parcelas=5;
        $lista=explode(',',$lista);
        foreach($lista as $turma){
            if(is_numeric($turma)){
                $db_turma=Turma::find($turma);
                if(count($db_turma)>0)
                    $turmas->push($db_turma);
            }

        }


        return view('secretaria.inscricao.turmas-escolhidas', compact('turmas'))->with('valor',$valor)->with('parcelas',$parcelas);

    }
    public static function csvTurmas($lista='0'){
        $turmas=collect();
        $valor=0;
        $parcelas=4;
        $lista=explode(',',$lista);
        foreach($lista as $turma){
            if(is_numeric($turma)){
                if(Turma::find($turma))
                    $turmas->push(Turma::find($turma));
            }
        }
        return $turmas->sortBy('hora_inicio');

    }

    public function turmasJSON(){
        $programas=Programa::get();
        foreach($programas as $programa){
            $turmas=Turma::where('turmas.programa',$programa->id)
                                        ->join('cursos','turmas.curso','=','cursos.id')
                                        ->leftjoin('disciplinas','turmas.disciplina','=','disciplinas.id')
                                        ->get();
            foreach($turmas as $turma){
                $dados[$programa->sigla][$turma->id]=$turmas;
            }
        }
        return $dados;
    }

    public function turmasSite(){

        
        $turmas=Turma::select('*', 'turmas.vagas as vagas','turmas.id as id')
                ->whereIn('turmas.status', ['inscricao','iniciada'])
                ->whereIn('turmas.local',[84,85,86])
                ->whereColumn('turmas.vagas','>','turmas.matriculados')
                ->join('cursos', 'turmas.curso','=','cursos.id')
                ->leftjoin('disciplinas', 'turmas.disciplina','=','disciplinas.id') 
                ->orderBy('cursos.nome')
                ->orderBy('disciplinas.nome')
                ->get();

        //return $turmas;

        
        return view('pedagogico.turma.turmas-site',compact('turmas'));
    }

    public function listarProfessores(){

        $professores=PessoaDadosAdministrativos::getFuncionarios('Educador');
        return view('docentes.lista-professores',compact('professores'));

    }
    public function turmasProfessor(Request $r){
        $turmas=Turma::select('*', 'turmas.vagas as vagas','turmas.id as id','turmas.matriculados as matriculados')
                ->whereIn('turmas.status', ['inscricao','iniciada'])
                ->where('professor',$r->professor)
                ->join('cursos', 'turmas.curso','=','cursos.id')
                ->leftjoin('disciplinas', 'turmas.disciplina','=','disciplinas.id')
                ->orderBy('cursos.nome')
                ->orderBy('disciplinas.nome')
                ->get();
        $professor=Pessoa::find($r->professor);

        //return $turmas;

        return view('pedagogico.turma.turmas-site',compact('turmas'))->with('professor',$professor->nomeSimples);

    }
    
    public function uploadImportaTurma(Request $request){
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($request->arquivo);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        //dd($spreadsheet);
        $pessoas = collect();
        for($i=2;$i<=$highestRow;$i++){
            if($spreadsheet->getActiveSheet()->getCell('D'.$i)->getValue() != null){
                $insc= (object)[];
                $insc->id = $i;
                $insc->nome=$spreadsheet->getActiveSheet()->getCell('D'.$i)->getValue();
                $insc->nascimento=$spreadsheet->getActiveSheet()->getCell('J'.$i)->getFormattedValue();
                $insc->nascimento = \Carbon\Carbon::createFromFormat('n/d/Y', $insc->nascimento)->format('d/m/Y');
                $insc->genero=$spreadsheet->getActiveSheet()->getCell('E'.$i)->getValue();
                $insc->fone=$spreadsheet->getActiveSheet()->getCell('I'.$i)->getValue();
                $insc->turma=$spreadsheet->getActiveSheet()->getCell('S'.$i)->getValue();
                

                $pessoas->push($insc);
            }
        }
        $pessoas = $pessoas->sortBy('nome');
        return view('pedagogico.turma.listar-importados')->with('pessoas',$pessoas)->with('arquivo',$request->arquivo);
    }
    public function processarImportacao(Request $request){
        $cadastrados = array();

        foreach ($request->pessoa as $id=>$key){ // para cada elemento do array pessoa (campo checkbox)
            $nascimento = \Carbon\Carbon::createFromFormat('d/m/Y', $request->nascimento[$id])->format('Y-m-d');
            
            if($key == 'on'){ //se o checkbox estiver marcado
                //verifica se já está cadastrado
                $cadastrado = Pessoa::where('nome','like',$request->nome[$id])->where('nascimento',$nascimento)->get();
                if(count($cadastrado) > 0 ){
                    // ja ta cadastrado
                    $pessoa = $cadastrado->first();
                }
                else{
                    //não cadastrado, cadastrar a pessoa
                    $pessoa = new Pessoa;
                    $pessoa->nome = $request->nome[$id];
                    $pessoa->nascimento = $nascimento;
                    $pessoa->genero = $request->genero[$id];
                    $pessoa->por = \Session::get('usuario');
                    $pessoa->save();


                }
                // se tiver o campo telefone estiver preenchido
                if(isset($request->telefone[$id])){
                    //procura pra ver se telefone já existe
                    $dado = PessoaDadosContato::where('dado','2')->where('pessoa',$pessoa->id)->where('valor', $request->telefone[$id]);
                    //cadastra se nao tiver
                    if(count($dado) == 0){
                        $telefone = new PessoaDadosContato;
                        $telefone->pessoa = $pessoa->id;
                        $telefone->dado = '2';
                        $telefone->valor = $request->telefone[$id];
                        $telefone->save();

                    }
                }
                //verifica se a turma existe
                $turma = Turma::find($request->turma[$id]);
                if($turma != null){
                    //Inscreve a pessoa (ele verifica antes se a pessoa está inscrita)
                    if(InscricaoController::inscreverAlunoSemMatricula($pessoa->id,$turma->id)){
                        $cadastrados[]=$id;
                    }
                }


            }
            
        }
        //return $cadastrados;
        return view('pedagogico.turma.confirmar-importados')->with('cadastrados',$cadastrados)->with('pessoas',$request->nome);
    }

    /**
     * acao em lote verifica alguma ação pedagogica nas turmas que receber por post
     */
    public function acaolote(Request $r){
        if(count($r->turmas) == 0)
                     return redirect()->back()->withErrors(['Não foi possivel efetuar sua solicitação: Nenhuma turma selecionada.']);
        switch ($r->acao) {
            case 'encerrar':
                foreach($r->turmas as $turma_id){
                    $turma = Turma::find($turma_id);
                    $this->finalizarTurma($turma);
                }
                return redirect()->back()->withErrors(['Turmas encerradas com sucesso']);
                break;
            case 'relancar':
                
                $programas=Programa::get();
                //$cursos=Curso::getCursosPrograma(); ok
                $professores=PessoaDadosAdministrativos::getFuncionarios('Educador');
                $unidades=Local::get(['id' ,'nome']);
                $parcerias=Parceria::orderBy('nome')->get();
                //Locais=Local::getLocaisPorUnidade($unidade);
                $dados=collect();
                $dados->put('programas',$programas);
                $dados->put('professores',$professores);
                $dados->put('unidades',$unidades);
                $dados->put('parcerias',$parcerias);

                //return $dados;

                return view('pedagogico.turma.recadastrar')->with('turmas',$r->turmas);
                break;
            case 'requisitos':
                $turmas = implode(',',$r->turmas);
                $requisitos = \App\Requisito::all();
                return redirect('/pedagogico/turmas/modificar-requisitos/'.$turmas);
                //return view('pedagogico.turma.turma-requisitos',compact('requisitos'))->with('turmas',$turmas);

                break;
            
            default:
                return redirect()->back()->withErrors(['Não foi possivel identificar sua solicitação.']);
                break;
        }
    }
    public function storeRecadastro(Request $r){
        //dd($r);
        //return $turma->hora_inicio;
        $turmas = explode(',', $r->turmas);
        foreach($turmas as $turma_id){


            $turma=Turma::findOrFail($turma_id);

            $novaturma = new Turma;
            if($r->programa > 0 )
                $novaturma->programa = $r->programa;
            else
                $novaturma->programa = $turma->programa->id;

            if($r->curso > 0 )
                $novaturma->curso = $r->curso;
            else
                $novaturma->curso = $turma->curso->id;

            if($r->disciplina > 0 )
                $novaturma->disciplina = $r->disciplina;
            else
                if(isset( $turma->disciplina))
                    $novaturma->disciplina = $turma->disciplina->id;

            if($r->professor > 0 )
                $novaturma->professor = $r->professor;
            else
                $novaturma->professor = $turma->professor->id;

            if($r->unidade > 0 )
                $novaturma->local = $r->unidade;
            else
                $novaturma->local = $turma->local->id;

            if($r->parceria > 0 )
                $novaturma->parceria = $r->parceria;
            else
                $novaturma->parceria = $turma->parceria;

            if($r->periodicidade != $turma->periodicidade )
                $novaturma->periodicidade = $r->periodicidade;
            else
                $novaturma->periodicidade  = $turma->periodicidade ;

            if(!empty($r->dias))
                $novaturma->dias_semana = $r->dias;
            else
                $novaturma->dias_semana = $turma->dias_semana;

            if($r->dt_inicio != '' )
                $novaturma->data_inicio = $r->dt_inicio;
            else
                $novaturma->data_inicio = \Carbon\Carbon::createFromFormat('d/m/Y', $turma->data_inicio, 'Europe/London')->format('Y-m-d');

            if($r->hr_inicio != '' )
                $novaturma->hora_inicio = $r->hr_inicio;
            else
                $novaturma->hora_inicio = $turma->hora_inicio;

            if($r->dt_termino != '' )
                $novaturma->data_termino = $r->dt_termino;
            else
                $novaturma->data_termino = \Carbon\Carbon::createFromFormat('d/m/Y', $turma->data_termino, 'Europe/London')->format('Y-m-d');

            if($r->hr_termino != '' )
                $novaturma->hora_termino = $r->hr_termino;
            else
                $novaturma->hora_termino = $turma->hora_termino;

            if($r->vagas != '' )
                $novaturma->vagas = $r->vagas;
            else
                $novaturma->vagas = $turma->vagas;

            if($r->valor != '' )
                $novaturma->valor = $r->valor;
            else
                $novaturma->valor = $turma->valor;

            if($r->carga != '' )
                $novaturma->carga = $r->carga;
            else
                $novaturma->carga = $turma->carga;

            $novaturma->status = 'espera';

            $novaturma->save();

        }
        //return $novaturma;
        return redirect()->back()->withErrors(['Turmas recadastradas com sucesso.']);
    }
    public static function listarTurmasDocente($docente){

        $turmas = Turma::where('professor', $docente)->whereIn('status',['inscricao','andamento','iniciada'])->orderBy('hora_inicio')->get();

        foreach($turmas as $turma){
            /**
             * [Ativars/desativar consulta de URL]
             * @var string
             */
            //$turma->url="/lista/".$turma->id;  //------------------------------------------------Apagar aqui
            

            $turma->weekday = \App\classes\Strings::convertWeekDay($turma->dias_semana[0]);

        }
    //dd($turmas->hora_inicio);
    $turmas = $turmas->sortBy('weekday');



    return $turmas;
    }
    public function getChamada($turma_id,$page,$opt){

        if($opt=='pdf')
            $opt='pdf&rel_pdf=rel_freq';

        if($page == 0)
            $url = "https://script.google.com/macros/s/AKfycbwY09oq3lCeWL3vHoxdXmocjVPnCEeZMVQgzhgl-J0WNOQPzQc/exec?id=".$turma_id."&portrait=false&tipo=".$opt;
        else
            $url = "https://script.google.com/macros/s/AKfycbwY09oq3lCeWL3vHoxdXmocjVPnCEeZMVQgzhgl-J0WNOQPzQc/exec?id=".$turma_id."&portrait=false&tipo=".$opt."&page=".$page;


        print '<div style="font-family:tahoma;font-size:10px;margin:50px auto 0;">Acessando sua lista em: '.$url.'</div><br>';

        $ch = curl_init();
        //não exibir cabeçalho
        curl_setopt($ch, CURLOPT_HEADER, false);
        // redirecionar se hover
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // desabilita ssl
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //envia a url
        curl_setopt($ch, CURLOPT_URL, $url);
        //executa o curl
        $result = curl_exec($ch);
        //encerra o curl
        curl_close($ch);



       $ws = json_decode($result);


       if( isset($ws{0}->url) )

            return redirect( $ws{0}->url);

        else

            return $result;
    }





    public function getPlano($professor,$tipo,$curso){

        print 'Carregando...';
      /* 
        if($tipo)
            $url = "https://script.google.com/macros/s/AKfycbwY09oq3lCeWL3vHoxdXmocjVPnCEeZMVQgzhgl-J0WNOQPzQc/exec?id_pro=".$professor."&id_disciplina=".$curso."&tipo=plano";
        else*/
            $url = "https://script.google.com/macros/s/AKfycbwY09oq3lCeWL3vHoxdXmocjVPnCEeZMVQgzhgl-J0WNOQPzQc/exec?id_pro=".$professor. "&id_curso=".$curso."&tipo=plano";
        //return $url;

        $ch = curl_init();
        //não exibir cabeçalho
        curl_setopt($ch, CURLOPT_HEADER, false);
        // redirecionar se hover
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // desabilita ssl
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //envia a url
        curl_setopt($ch, CURLOPT_URL, $url);
        //executa o curl
        $result = curl_exec($ch);
        //encerra o curl
        curl_close($ch);



       $ws = json_decode($result);


       if( isset($ws{0}->url) )

            return redirect( $ws{0}->url);

        else

            return $result;
    }








    /**
     * Finaliza turma individualmente
     * @param  [Turma] $turma [Objeto Turma]
     * @return [type]        [description]
     */
    public function finalizarTurma($turma){
       
        //localizar a turma

        $inscricoes = Inscricao::where('turma', $turma->id)->get();
       
        $turma->status = 'encerrada';
        $turma->save();

        //Inscricao->finalizar
        foreach($inscricoes as $inscricao){

            $executar = InscricaoController::finaliza($inscricao);

        }

   

        return $turma;

    }

    public function processarTurmasExpiradas(){
        $turmas_finalizadas = 0;

        $turmas = Turma::where('data_termino','<', date('Y-m-d'))->whereIn('status',['iniciada','andamento'])->get();



        //dd($turmas);

        //selecionar todas as turmas 
        foreach($turmas as $turma){

            $this->finalizarTurma($turma);
            $turmas_finalizadas ++;

        }

        return redirect($_SERVER['HTTP_REFERER'])->withErrors([$turmas_finalizadas.' turmas estavam expiradas e foram finalizadas.']);


    }
    public function modificarRequisitosView($turma){
        

    }

    public function atualizarInscritos(){
        $turmas = Turma::select(['id','matriculados'])->get();
        foreach($turmas as $turma){
            $inscritos = Inscricao::where('turma',$turma->id)->whereIn('status',['regular','pendente','finalizada','finalizado'])->count();
            if($turma->matriculados != $inscritos){
                $turma->matriculados = $inscritos;
                $turma->save();
            }

        }
        return "Turmas atualizadas.";
    }


        
        



}
