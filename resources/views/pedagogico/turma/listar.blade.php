 @extends('layout.app')
@section('pagina')
<div class="title-block">
    <h3 class="title"> Turmas disponíveis</h3>
</div>
@include('inc.errors')



<div class="subtitle-block">
    <h3 class="subtitle"><small>Matrícula de:</small> Adauto Junior</h3>
</div>
<form name="item" class="form-inline">
	<section class="section">
    <div class="row ">
        <div class="col-xl-12">
            <div class="card sameheight-item">
                <div class="card-block">
                    <!-- Nav tabs -->
                    <div class="row">
                    	<div class="col-xs-6 text-xs">
                            <div class="action dropdown"> 
                                <button class="btn  rounded-s btn-secondary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Com os selecionados...
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu1"> 
                                    <a class="dropdown-item" href="#" onclick="renovar()">
                                        <i class="fa fa-retweet icon"></i>Liberar período de matrículas
                                    </a> 
                                    <a class="dropdown-item" href="#" onclick="ativar()" data-toggle="modal" data-target="#confirm-modal">
                                        <i class="fa fa-unlock icon"></i> Cancelar período de matrículas
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="desativar()" data-toggle="modal" data-target="#confirm-modal">
                                        <i class="fa fa-lock icon"></i> Cancelar Matrículas
                                    </a>
                                </div>
                             </div>
                        </div>
                        <div class="title-block text-xs-right">
                            <h3 class="subtitle"><small>Matrícula de:</small> Adauto Junior</h3>
                        </div>
                    </div>
                    
                    <ul class="nav nav-tabs nav-tabs-bordered ">
                        <li class="nav-item"> <a href="" class="nav-link active" data-target="#todos" data-toggle="tab" aria-controls="todos" role="tab">Todos</a> </li>
                        <li class="nav-item"> <a href="" class="nav-link" data-target="#ce" aria-controls="ec" data-toggle="tab" role="tab">CE</a> </li>
                        <li class="nav-item"> <a href="" class="nav-link" data-target="#emg" aria-controls="emg" data-toggle="tab" role="tab">EMG</a> </li>
                        <li class="nav-item"> <a href="" class="nav-link" data-target="#pid" aria-controls="pid" data-toggle="tab" role="tab">PID</a> </li>
                        <li class="nav-item"> <a href="" class="nav-link" data-target="#uati" aria-controls="uati" data-toggle="tab" role="tab">UATI</a> </li>
                        <li class="nav-item"> <a href="" class="nav-link" data-target="#unit" aria-controls="unit" data-toggle="tab" role="tab">UNIT</a> </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content tabs-bordered">
        
                        <div class="tab-pane fade in active" id="todos">
                        	<h4>Todos os programas</h4>
                            <section class="example">
                        <div class="table-flip-scroll">



                        <ul class="item-list striped">
                            <li class="item item-list-header hidden-sm-down">
                                <div class="item-row">
                                    <div class="item-col fixed item-col-check">
                                        <label class="item-check" id="select-all-items">
                                        <input type="checkbox" class="checkbox">
                                        <span></span>
                                        </label> 
                                    </div>
                                    
                                    <div class="item-col item-col-header item-col-title">
                                        <div> <span>Curso</span> </div>
                                    </div>
                                    <div class="item-col item-col-header item-col-sales">
                                        <div> <span>Professor</span> </div>
                                    </div>

                                    <div class="item-col item-col-header item-col-sales">
                                        <div> <span>Vagas</span> </div>
                                    </div>
                                    <div class="item-col item-col-header item-col-sales">
                                        <div> <span>Valor</span> </div>
                                    </div>

                                    <div class="item-col item-col-header fixed item-col-actions-dropdown"> </div>
                                </div>
                            </li>
                            @foreach($dados->all() as $turma)
                            <li class="item">
                                <div class="item-row">
                                    <div class="item-col fixed item-col-check"> 

                                        <label class="item-check" id="select-all-items">
                                        <input type="checkbox" class="checkbox" nome="turma[]" value="{{$turma->id}}">
                                        <span></span>
                                        </label>
                                    </div>
                                    
                                    <div class="item-col fixed pull-left item-col-title">
                                        <div class="item-heading">Curso/atividade</div>
                                        <div>
                                            <a href="#"> Turma {{$turma->id}} </a>
                                            <a href="pessoas_show.php?id=1" class="">
                                                <h4 class="item-title"> {{$turma->curso->nome}}</h4></a>
                                             {{implode(', ',$turma->dias_semana)}} - {{$turma->hora_inicio}} ás {{$turma->hora_termino}}
                                        </div>
                                    </div>
                                    <div class="item-col item-col-sales">
                                        <div class="item-heading">Professor(a)</div>
                                        <div> {{$turma->professor->nome_simples}}
                                            <div>{{$turma->local->unidade}}</div>
                                        </div>
                                    </div>
                                    <div class="item-col item-col-sales">
                                        <div class="item-heading">Vagas</div>
                                        <div>{{$turma->vagas}}</div>
                                    </div>
                                     
                                   
                                    <div class="item-col item-col-sales">
                                        <div class="item-heading">Valor</div>
                                        <div>R$ {{$turma->valor}} </div>
                                    </div>

                                    <div class="item-col fixed item-col-actions-dropdown">
                                        <div class="item-actions-dropdown">
                                            <a class="item-actions-toggle-btn"> <span class="inactive">
                                    <i class="fa fa-cog"></i>
                                </span> <span class="active">
                                <i class="fa fa-chevron-circle-right"></i>
                                </span> </a>
                                            <div class="item-actions-block">
                                                <ul class="item-actions-list">
                                                    <li>
                                                        <a class="remove" href="#" data-toggle="modal" data-target="#confirm-modal"> <i class="fa fa-trash-o "></i> </a>
                                                    </li>
                                                    <li>
                                                        <a class="edit" href="item-editor.html"> <i class="fa fa-pencil"></i> </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li> 
                            @endforeach
                        </ul>


                            






                        </div>
                    </section>
                        </div>
                        <div class="tab-pane fade" id="ce">
                            <h4>Escola Municipal de Governo</h4>
                            <section class="example">
                        <div class="table-flip-scroll">
                            <table class="table table-striped table-bordered table-hover flip-content">
                                <thead class="flip-header">
                                    <tr> 
                                        <th>S</th>
                                        <th>Curso</th>
                                        <th>Professor/Unidade</th>
                                        <th>Vagas</th>
                                        <th>Valor</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach($dados->all() as $turma)
                                    <tr class="odd gradeX">
                                        <td>
                                            <div class="item-col fixed item-col-check">
                                                <label class="item-check" id="select-all-items">
                                                    <input type="checkbox" class="checkbox">
                                                    <span></span>
                                                </label> 
                                            </div>
                                        </td>
                                        <td><a href="#"> Turma {{$turma->id}} </a><br>
                                            <a href="pessoas_show.php?id=1" class="">
                                                {{$turma->curso->nome}}</a><br>
                                             {{implode(', ',$turma->dias_semana)}} - {{$turma->hora_inicio}} ás {{$turma->hora_termino}}</td>
                                        <td>{{$turma->professor->nome_simples}}
                                            <div>{{$turma->local->unidade}}</div></td>
                                        <td>{{$turma->vagas}}</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                    @endforeach
                                    
                                   
                                </tbody>
                            </table>
                        </div>
                    </section>
                        </div>
                        <div class="tab-pane fade" id="pid">
                            <h4>Programa de Inclusão Digital</h4>
                            <section class="example">
                        <div class="table-flip-scroll">
                           <table class="table table-striped table-bordered table-hover flip-content">
                                <thead class="flip-header">
                                    <tr> 
                                        <th>S</th>
                                        <th>Curso</th>
                                        <th>Professor</th>
                                        <th>Dia(s)</th>
                                        <th>Horário</th>
                                        <th>Local</th>
                                        <th>Vagas</th>
                                        <th>Valor</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="odd gradeX">
                                        <td>
                                            <div class="item-col fixed item-col-check">
                                                <label class="item-check" id="select-all-items">
                                                    <input type="checkbox" class="checkbox">
                                                    <span></span>
                                                </label> 
                                            </div>
                                        </td>
                                        <td>Yoga para o trabalho</td>
                                        <td>Adilson</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                    <tr class="odd gradeX">
                                        <td>
                                            <div class="item-col fixed item-col-check">
                                                <label class="item-check" id="select-all-items">
                                                    <input type="checkbox" class="checkbox">
                                                    <span></span>
                                                </label> 
                                            </div>
                                        </td>
                                        <td>Dança Chill</td>
                                        <td>Rita</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                    <tr class="odd gradeX">
                                        <td>
                                            <div class="item-col fixed item-col-check">
                                                <label class="item-check" id="select-all-items">
                                                    <input type="checkbox" class="checkbox">
                                                    <span></span>
                                                </label> 
                                            </div>
                                        </td>
                                        <td>Yoga para o trabalho</td>
                                        <td>Adilson</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                        </div>
                    </section>
                        </div>
                        <div class="tab-pane fade" id="emg">
                            <h4>Escola Municipal de Governo</h4>
                            <section class="example">
                        <div class="table-flip-scroll">
                            <table class="table table-striped table-bordered table-hover flip-content">
                                <thead class="flip-header">
                                    <tr> 
                                    	<th>S</th>
                                        <th>Curso</th>
                                        <th>Professor</th>
                                        <th>Dia(s)</th>
                                        <th>Horário</th>
                                        <th>Local</th>
                                        <th>Vagas</th>
                                        <th>Valor</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Yoga para o trabalho</td>
                                        <td>Adilson</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Dança Chill</td>
                                        <td>Rita</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Yoga para o trabalho</td>
                                        <td>Adilson</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                        </div>
                    </section>
                        </div>
                        <div class="tab-pane fade" id="pid">
                            <h4>Programa de Inclusão Digital</h4>
                            <section class="example">
                        <div class="table-flip-scroll">
                           <table class="table table-striped table-bordered table-hover flip-content">
                                <thead class="flip-header">
                                    <tr> 
                                    	<th>S</th>
                                        <th>Curso</th>
                                        <th>Professor</th>
                                        <th>Dia(s)</th>
                                        <th>Horário</th>
                                        <th>Local</th>
                                        <th>Vagas</th>
                                        <th>Valor</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Yoga para o trabalho</td>
                                        <td>Adilson</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Dança Chill</td>
                                        <td>Rita</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Yoga para o trabalho</td>
                                        <td>Adilson</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                        </div>
                    </section>
                        </div>
                        <div class="tab-pane fade" id="uati">
                            <h4>Universidade Aberta da Terceira Idade</h4>
                            <section class="example">
                        <div class="table-flip-scroll">
                            <table class="table table-striped table-bordered table-hover flip-content">
                                <thead class="flip-header">
                                    <tr> 
                                    	<th>S</th>
                                        <th>Curso</th>
                                        <th>Professor</th>
                                        <th>Dia(s)</th>
                                        <th>Horário</th>
                                        <th>Local</th>
                                        <th>Vagas</th>
                                        <th>Valor</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Yoga para o trabalho</td>
                                        <td>Adilson</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Dança Chill</td>
                                        <td>Rita</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Yoga para o trabalho</td>
                                        <td>Adilson</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                        </div>
                    </section>
                        </div>
                        <div class="tab-pane fade" id="unit">
                            <h4>Universidade Aberta do Trabalhador</h4>
                            <section class="example">
                        <div class="table-flip-scroll">
                            <table class="table table-striped table-bordered table-hover flip-content">
                                <thead class="flip-header">
                                    <tr> 
                                    	<th>S</th>
                                        <th>Curso</th>
                                        <th>Professor</th>
                                        <th>Dia(s)</th>
                                        <th>Horário</th>
                                        <th>Local</th>
                                        <th>Vagas</th>
                                        <th>Valor</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Costura</td>
                                        <td>Fabiana</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Espanhol</td>
                                        <td>Carla</td>
                                        <td>Seg. Qua. Sex.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                    <tr class="odd gradeX">
                                        <td>
                                        	<div class="item-col fixed item-col-check">
												<label class="item-check" id="select-all-items">
													<input type="checkbox" class="checkbox">
													<span></span>
												</label> 
											</div>
										</td>
                                        <td>Auxiliar Administrativo</td>
                                        <td>Claudia</td>
                                        <td>Ter. Qui.</td>
                                        <td>7:00-8:00</td>
                                        <td>FESC 1</td>
                                        <td>20</td>
                                        <td>R$ 200,00</td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                        </div>
                    </section>
                        </div>
                    </div>
                </div>
                <!-- /.card-block -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col-xl-6 -->
        
        <!-- /.col-xl-6 -->
    </div>
</section>

<div class="card-block">
	<a class="btn btn-primary" href="matricula_confirma_cursos.php">Avançar</a>
	
	<button class="btn btn-secondary">Limpar</button>
</div>

</form>
@endsection