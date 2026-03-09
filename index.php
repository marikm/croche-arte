<?php

    session_start();
    require_once("./config/conexao.php");
    // include("./views/site/home.php");
    $url = isset($_GET['url']) ? $_GET['url'] : 'home';
    
    $urlParts = explode('/', rtrim($url, '/'));
    
    $rota = $urlParts[0];

    switch($rota) {
        case 'home':
            require_once 'views/site/home.php';
            break;

        case 'produto':
            // pega o id do produto 
            // ex: produto/5 -> id= 5
            $_GET['id'] = isset($urlParts[1]) ? $urlParts[1] : 1;
            require_once 'views/site/detalhes.php';
            break;

        case 'carrinho':
            require_once 'views/site/carrinho.php';
            break;

        case 'api-carrinho':
            require_once 'carrinho_acao.php';
            break;

        case 'checkout':
            require_once 'views/site/checkout.php';
            break;

        // -- ROTAS DO ADMINSTRADOR --

        case 'admin':
            require_once 'views/admin/dashboard.php';
            break;
        
        default:
            // pagina não encontrada (Erro 404)
            http_response_code(404);
            echo "<h1> 404 - Página não encontrada </h1>";
            echo "<p>A rota '$rota' não existe no sistema. </p>";
            break;
    }