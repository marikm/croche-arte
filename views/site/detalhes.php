<?php  
    require_once("config/conexao.php");
    require_once("src/Models/Produto.php");
    include_once("views/includes/header.php");
?>

<style>
    .btn-cor.selecionado, .btn-tamanho.selecionado {
        border: 2px solid #000;
        transform: scale(1.1);
        box-shadow: 0 0 5px rgba(0,0,0,0.3);
    }
</style>
<?php
    
    $id_produto = $_GET['id'] ?? 1;
    $produtoObj = new Produto($pdo);
    $infoProduto = $produtoObj->buscarPorId($id_produto);
    $listaVariacoes = $produtoObj->buscarVariacoes($id_produto);

    $jsonVariacoes = [];

    

    // 1. Processar dados para separar opçoes de Cores e Tamanhos
    $coresUnicas = [];
    $tamanhosUnicos = [];

    
    foreach($listaVariacoes as $var) {

        // Agrupa cores (evita repeticao se tiver P, M, G da mesma cor)
        if(!in_array($var['id_cor'], $coresUnicas)) {
            $coresUnicas[$var['id_cor']] = [
                'nome' => $var['nome_cor'],
                'foto' => $var['foto'] // foto da variação
            ];
        }

        // Agrupa tamanhos unicos 
        if(!in_array($var['tamanho'], $tamanhosUnicos)) {
            $tamanhosUnicos[] = $var['tamanho'];
        }

        // Lógica para peças de decoração x roupas
        $labelTamanho = "Tamanho";
        if(in_array($infoProduto['nome_categoria'], ['Tapetes', 'Decoração'])) {
            $labelTamanho = "Dimensões";
        }

        // Montar mapa de variações JSON para verificar combinações depois 
        // chave = "ID_COR-TAMANHO" -> Valor = ID_VARIANTE
        $chave = $var["id_cor"] . "-" . $var["tamanho"];
        $jsonVariacoes[$chave] = [
            "id" => $var["id_variante"],
            "preco" => $var["precoUnitario"],
            "prazo" => $var["prazoProducao"]
        ];

    }
    ?>
    

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <img src="<?= '../uploads/produtos/'.$infoProduto["foto"] ?>" style="height:400px; fit-object-fit:cover" id="foto-principal" class="img-fluid rounded" alt="foto-principal-produto">
            </div>
            <div class="col-md-6">
                <h1><?= $infoProduto["nome"] ?></h1>
                <p class="text-muted"><?= $infoProduto["descricao"] ?></p>

                <h2 id="preco-display" class="text-primary">R$ <?= number_format($listaVariacoes[0]["precoUnitario"],2,',', '.') ?></h2>
                <p id="prazo-display" class="small text-muted">
                    Produção: <?= $listaVariacoes[0]["prazoProducao"] ?> dias úteis
                </p>

                <form action="carrinho.php" method="post" id="form-comprar">
                    <input type="hidden" name="id_variante" id="id_variante_selecionada" required>

                    <div class="mt-4">
                        <label class="mt-4">Cor / Modelo: </label>
                        <div class="selecao-cor">
                            <?php foreach ($coresUnicas as $idCor => $dadosCor) { ?>
                                <div class="btn-cor" 
                                    onclick="selecionarCor(<?= $idCor ?>, '<?= $dadosCor['foto'] ?>') "
                                    data-cor="<?= $idCor ?>">
                                    <img src= "<?='../uploads/produtos/' . $dadosCor['foto']?>" 
                                    alt="<?= $dadosCor['nome'] ?>" title="<?= $dadosCor['nome'] ?>">
                                </div>
                            <?php } ?>
                        </div>
                        <input type="hidden" id="cor_selecionada">
                    </div>
                    <div class="mt-3">
                        <label class="fw-bold"><?= $labelTamanho ?></label>
                        <div class="selecao-tamanho">
                            <?php foreach ($tamanhosUnicos as $tam) { ?>
                                <div class="btn-tamanho" onclick="selecionarTamanho('<?= $tam ?>')" data-tamanho="<?= $tam ?>">
                                    <?= $tam ?>
                                </div>
                            <?php } ?>
                        </div>
                        <input type="hidden" id="tamanho_selecionado">
                    </div>
                    <div class="d-flex align-items-center">
                        <label for="" class="me-2">Quantidade</label>
                        <div class="input-group" style="width:130px;">
                            <button class="btn btn-outline-secondary" type="button" id="btnMenos">-</button>
                            <input type="text" name="quantidade" class="form-control text-center" id="inputQuantidade" min="1" value="1" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="btnMais">+</button>
                            
                        </div>
                    </div>

                    <button type="button" class="btn btn-dark btn-lg mt-4 w-100 " id="btn-comprar" onclick="adicionarItem()" disabled>
                        Adicionar ao Carrinho 
                    </button>
                    <p id="msg-erro" class="text-danger mt-2" style="display:none">Combinação Indisponível</p>
                </form>
            </div>
        </div>
    </div>

    <script>
        //
        // Atualizar quantidade 
        //
        const qnt = document.getElementById('inputQuantidade');
        const btnMais = document.getElementById('btnMais');
        const btnMenos = document.getElementById('btnMenos');

        btnMais.addEventListener("click", () => {
            qnt.value = parseInt(qnt.value) + 1;
        });

        btnMenos.addEventListener("click", () => {
            if(parseInt(qnt.value) > 1) {
                qnt.value = parseInt(qnt.value) - 1;
            }
        })

        function adicionarItem() {
            // Pegar id da variação 
            const idVariante = document.getElementById('id_variante_selecionada').value;
            const quantidade = document.getElementById('inputQuantidade').value;

            // Monta dados para enviar ao servidor 
            const formData = new FormData();
            formData.append('id_variante', idVariante);
            formData.append('quantidade', quantidade);

            // Muda o texto do botão para dar feedback ao cliente
            const btnComprar = document.getElementById('btn-comprar');
            const textoOriginal = btnComprar.innerHTML;
            btnComprar.innerHTML = 'Adicionando... ⏳';
            btnComprar.disabled = true;

            // Envia requisição para o PHP
            fetch("/croche-arte/api-carrinho", {
                method: 'POST',
                body:formData
            })
            .then(response => response.json()) // converte resposta para OBJETO json
            .then(data => {
                if(data.sucesso){
                    document.getElementById('badge-carrinho').innerText = data.total_itens;

                    // Animação/feedback visual no botao 
                    btnComprar.innerHTML = "✓ Adicionado!";
                    btnComprar.classList.replace("btn-dark", 'btn-success');

                    // Volta o botão para o padroa depois de 2 segundos
                    setTimeout(() => {
                        btnComprar.innerHTML = textoOriginal;
                        btnComprar.classList.replace('btn-success', 'btn-dark');
                        btnComprar.disabled = false;
                    }, 2000);
                } else {
                    alert('Erro ao adicionar:' + data.mensagem);
                    btnComprar.innerHTML = textoOriginal;
                    btnComprar.disabled = false;
                }
            })
            .catch(error => {
                console.error("Erro na requisição:", error);
                alert("Ocorreu um erro ao comunicar com o servidor.");
                btnComprar.innerHTML = textoOriginal;
                btnComprar.disabled = false;
            })
        }




        // Recebe o array PHP como objeto JS 
        const mapaVariacoes = <?php echo json_encode($jsonVariacoes) ?>

        console.log(mapaVariacoes);

        function selecionarCor(idCor, foto) {
            // 1. Atualiza visual 
            document.querySelectorAll(".btn-cor").forEach(el => el.classList.remove('selecionado'));
            // Seleciona um botao 

            document.querySelector(`.btn-cor[data-cor="${idCor}"]`).classList.add('selecionado');
        

            // 2. Atualiza a foto principal  
            if(foto) {
               document.getElementById('foto-principal').src = '../uploads/produtos/' + foto;
            }

            // 3. Salva estado 
            document.getElementById('cor_selecionada').value = idCor;
            atualizarEstadoFinal();
        }

        function selecionarTamanho(tamanho) {
            document.querySelectorAll(".btn-tamanho").forEach(el => el.classList.remove('selecionado'));
            // muda a classe do botao para mudar a aparencia
            document.querySelector(`.btn-tamanho[data-tamanho="${tamanho}"]`).classList.add('selecionado');

            document.getElementById('tamanho_selecionado').value = tamanho
            atualizarEstadoFinal();


        }

        function atualizarEstadoFinal() {
            const cor = document.getElementById('cor_selecionada').value;
            const tam = document.getElementById('tamanho_selecionado').value;
            const btnComprar = document.getElementById('btn-comprar');
            const msgErro = document.getElementById('msg-erro');

            const chave = cor + '-' + tam;
           
            // verificando disponibilidade de combinação
            if(Object.keys(mapaVariacoes).includes(chave)) {
                // console.log("Combinação encontrada");
                const variacao = mapaVariacoes[chave];
                // atualiza o input que salva a variação escolhida 
                document.getElementById("id_variante_selecionada").value = variacao.id;

                // Atualiza preço e prazo na tela 
                document.getElementById('preco-display').innerText = 'R$ ' + parseFloat(variacao.preco).toFixed(2).replace('.', ',');
                document.getElementById('prazo-display').innerText = 'Produção: ' + variacao.prazo + ' dias úteis';

                // Liberar a compra
                btnComprar.classList.remove('desabilitado');
                btnComprar.disabled = false;
                msgErro.style.display = 'none';
            } else {
                btnComprar.disabled = true;
                msgErro.style.display = 'block';
                msgErro.innerText = "Esta combinação não está disponível.";

                //console.log("Combinação não encontrada");
            }
        }
    </script>

  <?php include "views/includes/footer.php"; ?>