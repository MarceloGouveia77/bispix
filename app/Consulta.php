<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\IndiceInvertido;

class Consulta extends Model
{
    public static function executar($query)
	{
        $tam = explode(' ', $query);
        $tam = count($tam);
        switch ($tam) {
			case 1:
				return self::verificaConsulta($query);
				break;
            case 3:
                return self::consultaLogica($query);
                break;
            case 2:
                return self::verificaConsultaDupla($query);
                break;
		}
	}

	private static function verificaConsulta($query){
        $entrada = explode('!', $query);

        if(strcmp($query[0], '!') == 0){
            return IndiceInvertido::consultaPorter($entrada[1]);
        }
        else{
            return IndiceInvertido::consultaSimples($query);
        }
    }

    private static function verificaConsultaDupla($query){
        $entrada = explode(' ', $query);
        $temp = '"';

        if(strcmp($entrada[0][0], $temp) == 0){
            return indiceInvertido::consultaFrase($entrada[0], $entrada[1]);
        }else{
            return IndiceInvertido::consultaNOT($entrada[1]);
        }
    }


    private static function consultaLogica($query){
        $entrada = explode(' ', $query);

        switch($entrada[1]){
            case 'or':
                return IndiceInvertido::consultaOR($entrada[0], $entrada[2]);
                break;
            case 'and':
                return IndiceInvertido::consultaAND($entrada[0], $entrada[2]);
                break;
            case 'xor':
                return IndiceInvertido::consultaXOR($entrada[0], $entrada[2]);
        }
    }

}
