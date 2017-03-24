<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\IndiceInvertido;

class Consulta extends Model
{
    public static function executar($query)
	{
        $tam = explode(' ', $query);
        //print_r($tam);
        $tam = count($tam);
        switch ($tam) {
			case 1:
				return self::consultaSimples($query);
				break;
            case 3:
                return self::consultaLogica($query);
                break;
            case 2:
                return self::consultaNOT($query);
                break;
		}
	}
	private static function consultaSimples($query)
	{
		return IndiceInvertido::postings($query);
	}

	private static function consultaLogica($query){
        $entrada = explode(' ', $query);
        switch($entrada[1]){
            case 'or':
                return self::consultaOR($entrada[0], $entrada[2]);
                break;
            case 'not':
                break;
        }
    }

    private static function consultaOR($query1, $query2){
        return IndiceInvertido::consultaOR($query1, $query2);
    }

    private static function consultaNOT($query1){
        $entrada = explode(' ', $query1);
        return IndiceInvertido::consultaNOT($entrada[1]);
    }
}
