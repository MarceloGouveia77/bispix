<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use PorterStemmer;

require_once 'PorterStemmer.php';

class IndiceInvertido extends Model
{
    protected $table = 'indice';

    private static function prepararBanco()
    {
        $nomeTabela = (new self)->getTable();

        if( ! Schema::hasTable($nomeTabela) ){
            Artisan::call('migrate:refresh');
            Artisan::call('db:seed');
        }
        else if(self::count() != 0){
            self::truncate();
        }
    }
    public static function tokenizer($nomeDiretorio)
    {
        $endAbsoluto = base_path().'/data/colecoes/'.$nomeDiretorio;
        $lista = self::listaDiretorio($endAbsoluto);
        $tam = count($lista);

        $log = base_path().'/data/colecoes/log.txt';

        $i=1;
        foreach($lista as $arq){

            $logFile = fopen($log,'w');
            $data = $i."-".$tam."-".$arq;
            fwrite($logFile, $data);
            fclose($logFile);
            $i++;

            $pont = fopen($endAbsoluto.'/'.$arq,'r');
            if($pont){
                $posicao = 0;
                while(true) {
                    $linha = fgets($pont);
                    if ($linha==null) break;
                    
                    $termos = preg_split("/[\s-]+/",$linha);
                    
                    foreach($termos as $t){
                        $tripla = new IndiceInvertido;
                        $valor = trim($t);

                        if( strlen($valor) ){
                            $posicao++;
                            $tripla->termo = $t;
                            $tripla->documento = $arq;
                            $tripla->posicao = $posicao;
                            $tripla->save();
                        }
                    }
                }
                fclose($pont);
            }
        }

        //Limpando o log do arquivo
        $logFile = fopen($log,'w');
        $data = "";
        fwrite($logFile, $data);
        fclose($logFile);
    }
    private static function listaDiretorio($nomeDiretorio)
    {

        $lista = scandir($nomeDiretorio);
        $chave = array_search(".", $lista);
        unset($lista[$chave]);
        $chave = array_search("..", $lista);
        unset($lista[$chave]);

        return $lista;
    }
    public static function normalizacao()
    {
        $triplas = self::all();
        $tam = $triplas->count();
        $log = base_path().'/data/colecoes/log.txt';

        $i = 1;
        foreach ($triplas as $t) {
            $logFile = fopen($log,'w');
            $data = $i."-".$tam;
            fwrite($logFile, $data);
            fclose($logFile);
            $i++;

            $termo = $t->termo;
            $termo = self::normalizar($termo);

            $t->termo = $termo;
            $t->save();
        }

        //Limpando o log do arquivo
        $logFile = fopen($log,'w');
        $data = "";
        fwrite($logFile, $data);
        fclose($logFile);
    }
    private static function normalizar($termo)
    {
        $simbolosRemocao =
            array(
                "?", "!", ",", ";", "(",
                ")", "\"", ":", "."
            );
        $termoNormalizado = str_replace($simbolosRemocao, "", $termo);
        $termoNormalizado = mb_strtolower($termoNormalizado);


        return $termoNormalizado;
    }

    public static function listaDocumento($query){
        $postings = IndiceInvertido::select('documento')
            ->where('termo', $query)
            ->lists('documento');

        return $postings;
    }

    public static function parametros($nomeMetodo)
    {
        switch($nomeMetodo){
            case 'passo-1':
                return self::parametrosPasso1();
                break;
            case 'passo-2':
                return self::parametrosPasso2();
                break;
            case 'passo-3':
                return self::parametrosPasso3();
                break;
            case 'passo-4':
                return self::parametrosPasso4();
                break;
            case 'fim':
                return self::parametrosFim();
                break;
        }
    }

    private static function parametrosPasso1()
    {
        self::prepararBanco();

        $data['viewName'] = 'site.gerar-indice.index';
        $data['panelName'] = 'site.gerar-indice.passo-1.index';
        $data['scriptName'] = 'block.script';

        $data['navAtivo'] = 'passo-1';
        $data['panelUrl'] = url('/gerar-indice/passo-2');
        $data['panelId'] = 'colecaoForm';
        $data['panelNext'] = 'Próximo';
        $data['panelIcon'] = 'forward';

        return $data;
    }

    private static function parametrosPasso2()
    {
        $data['viewName'] = 'site.gerar-indice.index';
        $data['panelName'] = 'site.gerar-indice.passo-2.index';
        $data['scriptName'] = 'site.gerar-indice.passo-2.script';

        $data['navAtivo'] = 'passo-2';
        $data['panelUrl'] = url('/gerar-indice/passo-3');
        $data['panelId'] = 'colecaoForm';
        $data['panelNext'] = 'Próximo';
        $data['panelIcon'] = 'forward';

        return $data;
    }

    private static function parametrosPasso3()
    {
        $data['viewName'] = 'site.gerar-indice.index';
        $data['panelName'] = 'site.gerar-indice.passo-3.index';
        $data['scriptName'] = 'site.gerar-indice.passo-3.script';

        $data['navAtivo'] = 'passo-3';
        $data['panelUrl'] = url('/gerar-indice/passo-4');
        $data['panelId'] = 'colecaoForm';
        $data['panelNext'] = 'Próximo';
        $data['panelIcon'] = 'forward';

        return $data;
    }

    private static function parametrosPasso4()
    {
        $data['viewName'] = 'site.gerar-indice.index';
        $data['panelName'] = 'site.gerar-indice.passo-4.index';
        $data['scriptName'] = 'block.script';

        $data['navAtivo'] = 'passo-4';
        $data['panelUrl'] = url('/gerar-indice/fim');
        $data['panelId'] = 'colecaoForm';
        $data['panelNext'] = 'Próximo';
        $data['panelIcon'] = 'forward';

        return $data;
    }

    private static function parametrosFim()
    {
        $data['viewName'] = 'site.gerar-indice.index';
        $data['panelName'] = 'site.gerar-indice.fim.index';
        $data['scriptName'] = 'block.script';

        $data['navAtivo'] = 'fim';
        $data['panelUrl'] = url('/');
        $data['panelId'] = 'colecaoForm';
        $data['panelNext'] = 'OK';
        $data['panelIcon'] = 'ok';

        return $data;
    }

    public static function listaDocumentos()
    {
        $postings = IndiceInvertido::select('documento')
            ->distinct()
            ->lists('documento');

        return $postings;
    }

    public static function listaPosicao($query){
        $postings = IndiceInvertido::select('posicao')
            ->where('termo', $query)
            ->lists('posicao');

        return $postings;
    }

    public static function intersecao($temp, $temp2){
        $postings = array();

        $tam = count($temp);
        $tam2 = count($temp2);

        $i = 0;
        $j = 0;

        while($i < $tam && $j < $tam2){
            if($temp[$i] < $temp2[$j])
                $i++;
            else if($temp2[$j] < $temp[$i])
                $j++;
            else{
                array_push($postings, $temp2[$j]);
                $i++;
                $j++;
            }
        }
        return $postings;
    }
    public static function consultaSimples($query)
    {
        $postings = IndiceInvertido::select('documento')
            ->where('termo', $query)
            ->distinct()
            ->lists('documento')
            ;
        return $postings;
    }
    public static function consultaAND($query1, $query2) // EXEMPLO -> player and game
    {
        if(is_numeric($query1[0])){
            $temp = $query1;
            $temp2 = $query2;
        }else{
        $temp = self::consultaSimples($query1);
        $temp2 = self::consultaSimples($query2);
        }

        $postings = self::intersecao($temp, $temp2);
        return $postings;
    }
    public static function consultaOR($query1, $query2) // EXEMPLO -> fifa or pes
    {   if(is_numeric($query1[0])){
            $temp = $query1;
            $temp2 = $query2;
        }else{
        $temp = self::consultaSimples($query1);
        $temp2 = self::consultaSimples($query2);
        }

        $postings = array();

        $tam = count($temp);
        $tam2 = count($temp2);

        $i = 0;
        $j = 0;

        while(($i < $tam) || ($j < $tam2)){
            if($i<$tam && $j<$tam2) {
                if ($temp[$i] < $temp2[$j]) {
                    array_push($postings, $temp[$i]);
                    $i++;
                } else if ($temp2[$j] < $temp[$i]) {
                    array_push($postings, $temp2[$j]);
                    $j++;
                } else {
                    array_push($postings, $temp2[$j]);
                    $i++;
                    $j++;
                }
            }
            else if($i < $tam && $j >= $tam2){
                array_push($postings, $temp[$i]);
                $i++;
            }
            else if($i >= $tam && $j < $tam2){
                array_push($postings, $temp2[$j]);
                $j++;
            }
        }
        return $postings;
    }
    public static function consultaXOR($query1, $query2) // EXEMPLO -> sims xor fifa
    {
        if(is_numeric($query1[0])){
            $temp = $query1;
            $temp2 = $query2;
        }else{
        $temp = self::consultaSimples($query1);
        $temp2 = self::consultaSimples($query2);
        }

        $postings = array();
        $tam = count($temp);
        $tam2 = count($temp2);

        $i = 0;
        $j = 0;

        while(($i < $tam) || ($j < $tam2)){
            if($i<$tam && $j<$tam2) {
                if ($temp[$i] < $temp2[$j]) {
                    array_push($postings, $temp[$i]);
                    $i++;
                } else if ($temp2[$j] < $temp[$i]) {
                    array_push($postings, $temp2[$j]);
                    $j++;
                } else {
                    $i++;
                    $j++;
                }
            }
            else if($i < $tam && $j >= $tam2){
                array_push($postings, $temp[$i]);
                $i++;
            }
            else if($i >= $tam && $j < $tam2){
                array_push($postings, $temp2[$j]);
                $j++;
            }
        }

        return $postings;
    }

    public static function consultaNOT($query1) // EXEMPLO -> not sims
    {
        if(is_numeric($query1[0])){
            $temp = $query1;
        }else{
        $temp = self::consultaSimples($query1);
        }

        $arrayDocs = self::listaDocumentos();

        $postings = array();
        $i = 0;
        $j = 0;
        $tam = count($arrayDocs);
        $tam2 = count($temp);

        while($i < $tam){
            if($j < $tam2){
                if($arrayDocs[$i] < $temp[$j]) {
                    array_push($postings, $arrayDocs[$i]);
                    $i++;
                }
                else if($temp[$j] < $arrayDocs[$i])
                    $j++;
                else{
                    $i++;
                    $j++;
                }
            }else if($j >= $tam2){
                array_push($postings, $arrayDocs[$i]);
                $i++;
            }
        }

        return $postings;
    }

    public static function consultaPorter($query) // EXEMPLO -> !working
    {
        $query2 = PorterStemmer::Stem($query);

        $postings = self::consultaOR($query, $query2);

        print_r($query2);
        return $postings;
    }

    public static function consultaFrase($palavra, $palavra2){ // EXEMPLO -> "the sims"
        $postings = array();
        $temp = explode('"', $palavra);
        $palavra = $temp[1];

        $temp2 = explode('"', $palavra2);
        $palavra2 = $temp2[0];

        $posQuery1 = self::listaPosicao($palavra);
        $docQuery1 = self::listaDocumento($palavra);

        $posQuery2 = self::listaPosicao($palavra2);
        $docQuery2 = self::listaDocumento($palavra2);

        $tamPos = count($posQuery1);
        $tamPos2 = count($posQuery2);

        $docsTemp = array();

        for($i = 0; $i<$tamPos; $i++){
            for($j = 0; $j<$tamPos2; $j++){
                if(($posQuery1[$i] == $posQuery2[$j] - 1) && ($docQuery1[$i] == $docQuery2[$j]))
                    array_push($docsTemp, $docQuery1[$i]);
            }
        }

        foreach($docsTemp as $doc){
            $igual = false;
            foreach($postings as $post){
                if($doc == $post) {
                    $igual = true;
                    break;
                }
            }
            if(!$igual)
                array_push($postings, $doc);
        }

        return $postings;
    }

    public static function notTermoBinTermo($query){ // EXEMPLO -> not fifa and pes
        $entrada = explode(' ', $query);

        $arrayQuery = self::consultaSimples($entrada[3]);
        $arrayNOT = self::consultaNOT($entrada[1]);
        
        switch ($entrada[2]) {
            case 'and':
                $postings = self::consultaAND($arrayNOT, $arrayQuery);
                break;
            case 'or':
                $postings = self::consultaOR($arrayNOT, $arrayQuery);
                break;
            case 'xor':
                $postings = self::consultaXOR($arrayNOT, $arrayQuery);
                break;
            default:
                return "ERRO";
                break;
        }

        return $postings;
    }

    public static function notParenteseBin($query){ // EXEMPLO -> not(fifa and pes)
        $entrada = explode(' ', $query);
        $palavra1 = explode('(', $entrada[1])[1];
        $palavra2 = explode(')', $entrada[3])[0];

        switch ($entrada[2]) {
            case 'and':
                $arrayAux = self::consultaAND($palavra1, $palavra2);
                break;
            case 'or':
                $arrayAux = self::consultaOR($palavra1, $palavra2);
                break;
            case 'xor':
                $arrayAux = self::consultaXOR($palavra1, $palavra2);
                break;
            default:
                return "erro";
                break;
        }

        $postings = self::consultaNOT($arrayAux);

        return $postings;
    }

    public static function termoBinNotTermo($query){ // EXEMPLO -> fifa and not pes
        $entrada = explode(' ', $query);
        $palavra1 = $entrada[0];
        $palavra2 = $entrada[3];

        $arrayQuery = self::consultaSimples($palavra1);
        $arrayNOT = self::consultaNOT($palavra2);


        print_r($arrayQuery);
        print_r($arrayNOT);
        switch ($entrada[1]) {
            case 'and':
                $postings = self::consultaAND($arrayQuery, $arrayNOT);
                break;
            case 'or':
                $postings = self::consultaOR($arrayQuery, $arrayNOT);
                break;
            case 'xor':
                $postings = self::consultaXOR($arrayQuery, $arrayNOT);
                break;
            default:
                return "erro";
                break;
        }

        return $postings;   
    }

    public static function notTermoBinNotTermo($query){ // EXEMPLO -> not game and not player
        $entrada = explode(' ', $query);
        $palavra1 = $entrada[1];
        $palavra2 = $entrada[4];

        $arrayNOT = self::consultaNOT($palavra1);
        $arrayNOT2 = self::consultaNOT($palavra2);

        switch ($entrada[2]) {
            case 'and':
                $postings = self::consultaAND($arrayNOT, $arrayNOT2);
                break;
            case 'or':
                $postings = self::consultaOR($arrayNOT, $arrayNOT2);
                break;
            case 'xor':
                $postings = self::consultaXOR($arrayNOT, $arrayNOT2);
                break;
            default:
                return "erro";
                break;
        }

        return $postings;   
    }
}
