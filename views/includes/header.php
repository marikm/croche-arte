<!-- Pegando os nomes das categorias -->

<?php
    require_once(__DIR__."/../../config/conexao.php");
    
    $sql = "SELECT nome FROM categorias";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_COLUMN,0);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link rel="stylesheet" href= "<?= BASE_URL ?>/assets/css/style.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>Crochê Arte 🧶</title>
</head>
<body>

   <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <!-- Brand -->
        <a href="/croche-arte/home" class="navbar-brand">
            <div id="bg-brand" class="rounded-3" style="width: 43px; height: 43px; display: flex; align-items: center; justify-content: center;">
                🧶
            </div>
        </a>
        
        <!-- Botão hamburger para mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Conteúdo que colapsa no mobile -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Categorias -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php foreach ($categorias as $categoria) { ?>
                    <li class="nav-item">
                        <a href="" class="nav-link" nowrap><?= $categoria ?></a>
                    </li>
                <?php } ?>
            </ul>
            
            <!-- Busca -->
            <form class="d-flex mx-auto" style="width: 400px;" role="search">
                <div class="input-group">
                    <input class="form-control" type="search" placeholder="Buscar peças de crochê" aria-label="Search">
                    <button class="btn btn-outline-secondary" style="border-color: #ced4da ;" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Ícones perfil e carrinho -->
            <div class="d-flex gap-3 ms-lg-3 mt-3 mt-lg-0">
                <a href="#" class="text-dark"><i class="bi bi-person fs-5"></i></a>
                <a href="/croche-arte/carrinho" class="text-dark position-relative">
                    <i class="bi bi-cart fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="badge-carrinho" style="font-size: 0.6rem;">
                        <?php 
                            // Inicia sessão se não estiver iniciada
                            if(session_status() === PHP_SESSION_NONE) { session_start();}

                            // Conta quantos itens já existem na sessão ao carregar a pagina
                            $totalItens = 0;
                            if(isset($_SESSION['carrinho'])) {
                                $totalItens = array_sum($_SESSION['carrinho']);
                            }
                            echo $totalItens;
                        ?>
                    </span>
                </a>
            </div>
        </div>
    </div>
</nav>
    

    
