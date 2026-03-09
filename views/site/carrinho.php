<?php
    session_start();
    require_once  __DIR__.'/../../config/conexao.php';

    // inicializar carrinho, se tiver na sessão ou cria um
    $carrinho = $_SESSION["carrinho"] ?? [];
    $itensCarrinho = [];
    $valorTotal = 0;
    $prazoTotalProducao = 0;
    $pesoTotal = 0;

    // Buscar dados dos produtos do carrinho no BD
    if (!empty($carrinho)) {
        // Pega todos os IDs que estão na sessão (ex: 1, 5, 12)
        $ids = implode(',', array_map("intval", array_keys($carrinho)));

        // Consulta SQL: produto pai, cor, detalhes da variacao
        $sql = "SELECT v.id_variante, v.tamanho tamanho, v.foto, v.prazoProducao, v.precoUnitario, v.peso,
        produtos.nome as produto_nome, cores.nome as cor_nome 
         FROM variacao v 
         JOIN cores ON v.id_cor = cores.id_cor 
         JOIN produtos ON v.id_produto = produtos.id_produto 
         WHERE v.id_variante IN ($ids)";

         $stmt = $pdo->query($sql);
         $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resultados as $row) {
            $id = $row['id_variante'];
            $quantidade = $carrinho[$id];
            $subtotalItem = $row["precoUnitario"] * $quantidade;

            // guarda as informações formatadas no array da tela
            $row['quantidade'] = $quantidade;
            $row['subtotal'] = $subtotalItem;
            $itensCarrinho[] = $row;

            // Vai somando os totais do pedido
            $valorTotal += $subtotalItem;
            $pesoTotal += ($row['peso'] * $quantidade);

            // SOMAR OS DIAS DE PRODUÇÃO baseados na quantidade
            $prazoTotalProducao +=($row['prazoProducao'] * $quantidade);

        }
    }

    include "views/includes/header.php";
    ?>
    <div class="container mt-5">
        <h2 class="mb-4">Carrinho de Compras</h2>

        <?php if(empty($itensCarrinho)) { ?> 
            <div class="alert alert-warning text-center">
                Seu carrinho está vazio <br><br>
                <a href="home" class="btn btn-dark"> Voltar as compras</a>
            </div>
        <?php } else { ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <?php foreach ($itensCarrinho as $item) { ?>
                                <div class="row mb-3 align-items-center border-bottom pb-3">
                                    <div class="col-md-2">
                                        <img src="/croche-arte/uploads/produtos/<?= $item['foto'] ?>" alt="<?=$item['produto_nome']?>" class="rounded" style="width:100px;height:100px; object-fit:cover;">
                                    </div>
                                    <div class="col-md-5">
                                        <h5 class="mb-0"><?= $item['produto_nome']?></h5>
                                        <small class="text-muted">
                                            Cor: <?=$item['cor_nome']?> | Tamanho : <?=$item['tamanho']?>
                                        </small>
                                        <div class="mt-2">
                                            <div class="input-group input-group-sm w-50">
                                                <span class="input-group-text">Qtd</span>
                                                <input type="number" class="form-control" value="<?=$item['quantidade']?>" min="1" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 text-end">
                                        <div class="text-muted small">R$ <?= number_format($item['precoUnitario'],2,',', '.')?> cada</div>
                                        <div class="fw-bold fs-5 text-primary">R$ <?=number_format($item['subtotal'], 2, ',', '.')?></div>
                                        <div class="text-muted small">+<?=($item['prazoProducao'] * $item['quantidade'])?> dias</div>
                                    </div>

                                    <div class="col-md-2 text-end">
                                        <form action="api-carrinho" method="post">
                                            <input type="hidden" name="acao" value="remover">
                                            <input type="hidden" name="id_variante" value="<?= $item['id_variante']?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Remover Item">🗑️ Remover</button>
                                        </form>
                                    </div>

                                </div>


                           <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Resumo do Pedido</h4>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal (<?= array_sum($_SESSION['carrinho']) ?> itens)</span>
                                <span>R$ <?= number_format($valorTotal, 2, ',', '.') ?></span>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span>Prazo de Confecção</span>
                                <span class="text-warning fw-bold"><?= $prazoTotalProducao ?> dias úteis</span>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label small">Calcular Frete</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="00000-000">
                                    <button class="btn btn-outline-secondary" type="button">Calcular</button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mb-4 mt-3">
                                <span class="fs-5 fw-bold">Total</span>
                                <span class="fs-5 fw-bold text-primary">R$ <?= number_format($valorTotal, 2, ',', '.')?></span>
                            </div>

                            <a href="checkout" class="btn btn-dark w-100 btn-lg">Finalizar Pedido</a>
                        </div>
                    </div>
                </div>

            </div>
        <?php } ?>
    </div>

    <?php 
    // O caminho do include agora parte da raiz do projeto
    include 'views/includes/footer.php'; 
    ?>