<div class="panel panel-primary">

    <div class="panel-heading">
        Resultados para <strong>{{ $query }}</strong>
    </div>

    <table class="table table-hover">

    @foreach ($postings as $id)
            <tr>
                <td>
                    <a href="" data-title="Documento {{ $id }}" data-route="{{ url('colecao/'.$enderecoColecao.'/'.$id) }}"
                    class="openModal" data-toggle="modal" data-target="#modalDocument">
                        Documento {{ $id }}
                    </a>    <br/>
                    <p>{{substr(@file_get_contents(base_path().'/data/colecoes/'.$enderecoColecao.'/'.$id.'.txt'), 0, 100 ) }}</p>
                </td>

            </tr>

        @endforeach
        {!! $postings->links() !!}

    </table>

    </div>

@include('site.resultados.documento-modal')
