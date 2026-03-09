<?php
    //
    //  Recebe os dados, salva na sessao carrinho, 
    //  devolve resposta em json: "carrinho possui X itens"
    //
    session_start();

    // Garatir que a resposta será em formato JSON para o JS ler
    header('Content-Type: application/json');

    // Verifica se recebeu o Id da variação via POST
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_variante'])) {

        $id_variante = (int) $_POST['id_variante'];
        $acao = $_POST['acao'] ?? "adicionar";

        // Se o carrinho nao existir, criar o carrinho
        if(!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        // Remover o item do carrinho
        if($acao == "remover") {
            if(isset($_SESSION['carrinho'][$id_variante])) {
                unset($_SESSION['carrinho'][$id_variante]); // apaga o item da sessão 
            }
            // redireciona para pagina do carrinho
            header('Location: ' . '/croche-arte/carrinho');
            exit;
        }

        if($acao == "adicionar") {
            $quantidade = isset($_POST['quantidade']) ? (int) $_POST['quantidade'] : 1;
            // Se já tiver o item no carrinho, ele adiciona a quantidade
            if(isset($_SESSION['carrinho'][$id_variante])) {
                $_SESSION['carrinho'][$id_variante] += $quantidade;
            } else {
                $_SESSION['carrinho'][$id_variante] = $quantidade;
            }
    
            // Calcula o total de peças no carrinho para atualizar a bolinha no icone de carrinho
            $total_itens = array_sum($_SESSION['carrinho']);
    
            // Devolve a resposta com sucesso 
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Produto adicionado com sucesso!',
                'total_itens' => $total_itens,
            ]);
            exit;
        }

    }
        // Se der errado (acessar direto pela URL sem mandar POST)
        echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida.']);


