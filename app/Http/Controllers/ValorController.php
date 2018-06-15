<?php
/*
update turmas set carga=100 where valor = 550 and programa in('1','2') and carga is null;
update turmas set carga=80 where valor = 440 and programa in('1','2') and  carga is null;
update turmas set carga=60 where valor = 330 and programa in('1','2') and  carga is null;
update turmas set carga=40 where valor = 220 and programa in('1','2') and  carga is null;
update turmas set carga=60 where valor = 660 and programa in('12') and  carga is null;
update turmas set carga=40 where valor = 440 and programa in('12') and  carga is null;
update turmas set carga=30 where valor = 330 and programa in('12') and  carga is null;
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Valor;
use App\Matricula;

class ValorController extends Controller
{
    //
    public static function valorMatricula($id_matricula)
    {

    	$matricula = Matricula::find($id_matricula);

    	if($matricula)
    	{
    		if($matricula->curso == 307)
    		{
    			$inscricoes = \App\Inscricao::where('matricula',$matricula->id)->whereIn('status',['regular','pendente'])->get();
    			switch (count($inscricoes)) {
    				case 0:
                        return 0;
                        break;
                    case 1:
                    	$valor = Valor::find(5);
                        return $valor;
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $valor = Valor::find(6);
                        return $valor;
                        break;
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                    case 10:
                        $valor = Valor::find(7);
                        return $valor;
                        break;
    			}
    			

    		}
    		else
    		{
    			//pega a primeira inscricao da matricula
    			$inscricao = \App\Inscricao::where('matricula',$matricula->id)->whereIn('status',['regular','pendente'])->first();



                $valor= Valor::where('programa',$inscricao->turma->programa->id)->where('carga',$inscricao->turma->carga)->where('curso',$inscricao->turma->curso->id)->first();

                if($valor)
                {
                    return $valor;//number_format($valor->valor,2,',','.'); 
                }
                else
                {


                    $valor= Valor::where('programa',$inscricao->turma->programa->id)->where('carga',$inscricao->turma->carga)->first();
                


                }
                if(isset($valor))
                    return $valor;//number_format($valor->valor,2,',','.');
                else

                    throw new \Exception("Erro ao acessar valor da turma:".$valor, 1);
                    
                    
                

    			//pegar programa e  carga horária
    			//listar se existe algum valor com programa e curso
    				//se sim retornar o valor
    				//se não verificar programa e carga horária
    		}
    	}

    }
}
