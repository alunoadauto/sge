@extends('layout.app')
@section('pagina')
@include('inc.errors')
@if(count($disciplina))
  <div class="title-block">
                        <h3 class="title"> Edição de disciplina <span class="sparkline bar" data-type="bar"></span> </h3>
                    </div>
                    <form name="item" method="POST">
                     {{csrf_field()}}
                        <div class="card card-block">
							<div class="form-group row"> 
								<label class="col-sm-2 form-control-label text-xs-right">
									Nome
								</label>
								<div class="col-sm-10"> 
									{{$disciplina->nome}}
								</div>
							</div>
							<div class="form-group row"> 
								<label class="col-sm-2 form-control-label text-xs-right">
									Programa
								</label>
								<div class="col-sm-10"> 
									{{$disciplina->programa->nome}}
								</div>
							</div>
							<div class="form-group row"> 
								<label class="col-sm-2 form-control-label text-xs-right">
									Descrição
								</label>
								<div class="col-sm-10"> 
									{{$disciplina->desc}}
								</div>
							</div>
							<div class="form-group row"> 
								<label class="col-sm-2 form-control-label text-xs-right">
									Nº de vagas
								</label>
								<div class="col-sm-4"> 
									{{$disciplina->vagas }}
								</div>
							</div>
							<div class="form-group row"> 
								<label class="col-sm-2 form-control-label text-xs-right">
									Carga horária
								</label>
								<div class="col-sm-4"> 
									{{ $disciplina->carga}}
								</div>
							</div> 
							
                        </div>
    </form>
@endif
@endsection