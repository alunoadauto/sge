@extends('layout.app')
@section('titulo')Solicitação de Bolsa de estudo @endsection
@section('pagina')

<div class="title-search-block">
    <div class="title-block">
        <h3 class="title">Alun{{$pessoa->getArtigoGenero($pessoa->genero)}}: {{$pessoa->nome}} 
            @if(isset($pessoa->nome_resgistro))
                ({{$pessoa->nome_resgistro}})
            @endif
           
        </h3>
        <p class="title-description"> <b> Cod. {{$pessoa->id}}</b> - Tel. {{$pessoa->telefone}} </p>
    </div>
</div>
@include('inc.errors')
<form name="item" method="post" enctype="multipart/form-data">
{{csrf_field()}}
    <div class="card card-block">
    	<div class="subtitle-block">
            <h3 class="subtitle"><i class=" fa fa-folder-open "></i> Solicitação de Bolsa / Desconto</h3>
            <small>Para solicitação do desconto, o aluno deve se matricular no curso pretendido.</small>
        </div>
		<div class="form-group row"> 
            <label class="col-sm-2 form-control-label text-xs-right">
                Tipo
            </label>
            <div class="col-sm-6"> 
                <select name="classificacao" class="c-select form-control boxed" ">
                    <option selected="true">Selecione uma opção</option>
                    <option value="prefeitura">Desconto para Funcionários Públicos (20%)</option>
                    <option value="fesc">Desconto Servidores Fesc (100%)</option>
                    <option value="emg">Desconto EMG (100%)</option>

                    <option value="socioeconomica">Bolsa Socioeconômica</option>
                    <option value="nis">Bolsa Beneficiário de Programa Social</option>
                    <option value="saude">Bolsa de encaminhamento da Saúde</option>
                    <option value="caps">Bolsa de encaminhamento CAPS</option>
                    <option value="cidadania">Bolsa de encaminhamento da Cidadania</option>
                    <option value="pmsc">Bolsa de encaminhamento PMSC</option>
                    
                </select>
            </div>
        </div>

		<div class="form-group row"> 
            <label class="col-sm-2 form-control-label text-xs-right">Matriculas ativas:</label>
            <div class="col-sm-10"> 

                <div>
                    @foreach($matriculas as $matricula)
                    <label>
                    <input class="checkbox" type="checkbox" name="matricula[]" value="{{ $matricula->id}}" >
                    <span>{{$matricula->id}} <small>({{$matricula->status}})</small> - {{$matricula->getNomeCurso()}}</span>
                    </label><br>
                    @endforeach
                </div>
            </div>        
        </div>

		<div class="form-group row">
			<label class="col-sm-2 form-control-label text-xs-right"></label>
			<div class="col-sm-10 col-sm-offset-2"> 
				<input type="hidden" name="pessoa" value="{{$pessoa->id}}">
                <button type="submit" name="btn"  class="btn btn-primary">Salvar</button>
                <button type="reset" name="btn"  class="btn btn-primary">Limpar</button>
                <button type="cancel" name="btn" class="btn btn-primary" onclick="history.back(-2);return false;">Cancelar</button>
			</div>
       </div>
    </div>
</form>
<section class="section">
    <div class="row">
        <div class="col-md-12 center-block">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="header-block">
                        <p class="title" style="color:white">Bolsas solicitadas</p>
                    </div>
                </div>

                <div class="card-block">
                    <small>
                        <table class="table">
                            <thead>
                    
                                <th class="col-md-1">Data</th>
                                <th class="col-md-3">Matrícula</th>
                                <th class="col-md-1">Status</th>
                                <th class="col-md-5">Obs</th>
                                <th class="col-md-2">Opções</th>
                               
                            </thead>
                            <tbody>
                                @foreach($bolsas as $bolsa)
                                <tr>
                                    <td class="col-md-1">{{$bolsa->created_at->format('d/m/Y')}}</td>
                                    <td class="col-md-3">{{$bolsa->matricula.', '.$bolsa->matricula2}}</td>
                                    <td class="col-md-1">{{$bolsa->status}}</td>
                                    <td class="col-md-5">{{$bolsa->obs}}</td>
                                    <td style="font-size: 1.3em;" class="col-md-2">
                                        <a href="../imprimir/{{$bolsa->id}}" title="Imprimir Requerimento e Parecer"><i class=" fa fa-print "></i></a>&nbsp;
                                         @if(file_exists('documentos/bolsas/requerimentos/'.$bolsa->id.'.pdf'))
                                        <a href="/documentos/bolsas/requerimentos/{{$bolsa->id}}.pdf" title="Visualizar documentos;">
                                            <i class=" fa fa-file-text "></i></a>&nbsp;
                                        @else
                                         <a href="../upload/{{$bolsa->id}}" title="Enviar requerimento"><i class=" fa fa-cloud-upload"></i></a>&nbsp;
                                        @endif
                                        
                                         @if(file_exists('documentos/bolsas/pareceres/'.$bolsa->id.'.pdf'))
                                        <a href="/documentos/bolsas/pareceres/{{$bolsa->id}}.pdf" title="Visualizar Parecer" >
                                            <i class=" fa fa-file-text-o "></i></a>&nbsp;
                                        @else

                                        <a href="../parecer/{{$bolsa->id}}" style="color:orange;" title="Enviar parecer"><i class=" fa fa-cloud-upload"></i></a>&nbsp;
                                        <a href="#" onclick="cancelar({{$bolsa->id}})" style="color:red;" title="Cancelar"><i class=" fa fa-times"></i></a>&nbsp;
                                        </td>
                                        @endif

                                        
                                    
                                </tr>

                                @endforeach
                                
                            </tbody>
                        </table>
                    </small>
                    
                    
                    
                                   
                </div>     
            </div>
        </div> 
    </div>
</section>
@endsection
@section('scripts')
<script type="text/javascript">
    function cancelar(id){
        if(confirm('Deseja mesmo cancelar a solicitação de bolsa '+id+' ?')){
             $(location).attr('href','/juridico/bolsas/status/cancelar/'+id);
        }
    }
</script>
@endsection