<?php
    require_once(__DIR__."/../../config/conexao.php");

    class Produto {
        private $pdo;

        public function __construct($pdo)
        {
            $this->pdo = $pdo;
        }

        // Busca registro de um produto
        public function buscarPorId($id_produto) {
            $sql = "SELECT p.*, c.nome as nome_categoria 
            FROM produtos p 
            JOIN categorias c ON p.categoria = c.id_categoria
            WHERE p.id_produto = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":id", $id_produto);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Busca variações (Tamanho/Dimensões, Cores, Preco, Prazo)

        public function buscarVariacoes($id_produto) {
            $sql = "SELECT v.id_variante, v.tamanho, v.precoUnitario, v.prazoProducao, v.foto,
            c.nome as nome_cor , c.id_cor id_cor
            FROM variacao v 
            JOIN cores c ON v.id_cor = c.id_cor
            WHERE v.id_produto = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":id", $id_produto);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
