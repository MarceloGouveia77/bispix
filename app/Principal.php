<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Colecao;

class Principal extends Model
{
    public static function parametros($nomeMetodo, $query)
	{
		switch($nomeMetodo){
			case 'resultados':
				return self::parametrosResultados($query);
            case 'paginacao':
                return self::arrayPaginacao($query);
		}
	}
	private static function parametrosResultados($query)
	{
		$data['enderecoColecao'] = Colecao::getEnderecoColecaoAtual();
		$data['viewName'] = 'site.resultados.index';
		$data['scriptName'] = 'site.resultados.script';

        //$data['postings'] = $data['postings']->paginate(5);
		//print_r($data['postings']);
        return $data;
	}
	private static function arrayPaginacao($query){
        $data = Consulta::executar($query);
        return $data;
    }
}
