<?php 
// Consulta sql: traz os produtos e o menor preço das variações 
$sql = "SELECT p.id_produto, p.nome, p.foto, c.nome as categoria_nome, 
        MIN(v.precoUnitario) as preco_minimo FROM produtos p 
        JOIN categorias c ON p.categoria = c.id_categoria 
        LEFT JOIN variacao v ON p.id_produto = v.id_produto 
        GROUP BY p.id_produto 
        ORDER BY p.id_produto DESC";
$stmt = $pdo->query($sql);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// echo "<pre>";
// print_r($produtos);
// echo "</pre>";


include "views/includes/header.php" ?>
    <div class="bg-light py-5 mb-5 text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Crochê Arte</h1>
            <p class="lead text-muted">Peças exclusivas, feitas a mão</p>
        </div>
    </div>
    <div class="container" id="vitrine">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold border-bottom pb-2">Nossos Produtos</h2>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 rows-cols-lg-4 g-4 mb-5">
            <?php if(empty($produtos)) { ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Nenhum produto cadastrado no momento. Volte em breve.
                    </div>
                </div>
            
            <?php } else { 
                foreach ($produtos as $p) { ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="/croche-arte/uploads/produtos/<?=$p["foto"]?>"  class="card-img-top" alt="<?=$p["nome"]?>" style="height:250px;object-fit:cover;">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-secundary mb-2 align-self-start text-secondary">
                                <?= $p['categoria_nome'] ?>
                            </span>
                             <h5 class="card-title fw-bold text-truncate" title="<?= $produto['nome'] ?>">
                                <?= $p['nome'] ?>
                            </h5>
                            <p class="card-text text-primary fw-bold mt-auto mb-3">
                                <?php if($p['preco_minimo']) { ?>
                                    <small class="text-muted fw-normal fs-6">A partir de</small><br>
                                    R$ <?= number_format($p['preco_minimo'], 2, ',', '.') ?>
                                <?php } else { ?>
                                    <span class="text-muted">Preço sob consulta</span>
                                <?php } ?>
                            </p>
                            <a href="/croche-arte/produto/<?= $p['id_produto']?>" class="btn btn-outline-dark w-100 mt-auto">Ver detalhes</a>
                        </div>
                    </div>
                </div>
            <?php 
                }
            } ?>
        </div>
    </div>



<?php include "views/includes/footer.php" ?>
