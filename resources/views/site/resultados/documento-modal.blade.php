<div class="modal fade" id="modalDocument">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Article 01</h4>
            </div>

            <script>
                function destacar(text) {
                    var re = new RegExp(text, "ig");
                    documentText.innerHTML = documentText.innerHTML.replace(re, '<span style= "background-color: #3399ff">'+text+'</span>');
                }
                function seletor(text){
                    var consLogica = text.split(' ');

                    if(text[0] == '"'){ // CONSULTA POR FRASE
                        var aux = text.split('"');
                        var palavra = aux[1];
                        destacar(palavra);
                    }else if(consLogica.length == 3){ // CONSULTAS AND, OR, XOR
                        destacar(consLogica[0]);
                        destacar(consLogica[2]);
                    }else{
                        destacar(text);
                    }
                }
            </script>


            <div class="modal-body">
                <p>
                <div id="documentText"></div>
                </p>
            </div>

            <?php

                    ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" onclick="seletor('{{$query}}')">DESTACAR AS OCORRENCIAS</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->