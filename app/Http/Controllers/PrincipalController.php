<?php

namespace App\Http\Controllers;
use DB;

use App\IndiceInvertido;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Principal;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PrincipalController extends Controller
{
    public function index()
	{

		return view('template.index');
	}

	public function resultados(Request $request)
	{
		$query = $request->input('query');
		$data = Principal::parametros('resultados', $query);
		$data['query'] = $query;

        $resultados = Principal::parametros('paginacao',$query);


        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $collection = new Collection($resultados);
        $per_page = 5;
        $currentPageResults = $collection->slice(($currentPage-1) * $per_page, $per_page)->all();
        $data['postings'] = new LengthAwarePaginator($currentPageResults, count($collection), $per_page);
        $data['postings']->setPath($request->url());

        return view('template.empty', $data);
	}
}
