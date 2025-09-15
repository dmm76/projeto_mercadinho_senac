<?php
namespace App\Models;

use App\Core\BD;
use PDO;

class Pedido {
    public static function criarComItens(int $clienteId, ?int $enderecoId, string $entrega, string $pagamento, array $carrinho): int {
        $pdo = BD::conn();
        $pdo->beginTransaction();
        try {
            // 1) Recalcula total e valida estoque
            $subtotal = 0.00;
            foreach ($carrinho as $pid => $item) {
                $p = $item['produto'];  // vindo do Produto::encontrarAtivo
                $q = (float)$item['quantidade'];
                if ($q <= 0) throw new \Exception("Quantidade inválida");

                // estoque atual (lock)
                $st = $pdo->prepare("SELECT quantidade FROM estoque WHERE produto_id=? FOR UPDATE");
                $st->execute([$pid]);
                $est = $st->fetchColumn();
                if ($est === false || (float)$est < $q) {
                    throw new \Exception("Estoque insuficiente para o produto #$pid");
                }

                $subtotal += ((float)$p['preco_atual']) * $q;
            }

            $frete = 0.00; $desconto = 0.00; // pode aplicar cupom depois
            $total = $subtotal - $desconto + $frete;

            // 2) cria pedido
            $sqlPed = "INSERT INTO pedido (cliente_id, endereco_id, status, entrega, pagamento, subtotal, frete, desconto, total)
                       VALUES (?, ?, 'novo', ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sqlPed)->execute([$clienteId, $enderecoId, $entrega, $pagamento, $subtotal, $frete, $desconto, $total]);
            $pedidoId = (int)$pdo->lastInsertId();

            // 3) itens + baixa estoque + mov_estoque
            $sqlItem = "INSERT INTO item_pedido (pedido_id, produto_id, quantidade, preco_unit)
                        VALUES (?, ?, ?, ?)";
            $stItem = $pdo->prepare($sqlItem);

            $stBaixa = $pdo->prepare("UPDATE estoque SET quantidade = quantidade - ? WHERE produto_id=?");
            $stMov   = $pdo->prepare("INSERT INTO mov_estoque (produto_id, tipo, quantidade, origem, referencia_id, observacao)
                                      VALUES (?, 'saida', ?, 'pedido', ?, 'Saída por venda')");

            foreach ($carrinho as $pid => $item) {
                $p = $item['produto'];
                $q = (float)$item['quantidade'];
                $preco = (float)$p['preco_atual'];

                $stItem->execute([$pedidoId, $pid, $q, $preco]);
                $stBaixa->execute([$q, $pid]);
                $stMov->execute([$pid, $q, $pedidoId]);
            }

            $pdo->commit();
            return $pedidoId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function listarDoCliente(int $clienteId): array {
        $st = BD::conn()->prepare("SELECT * FROM pedido WHERE cliente_id=? ORDER BY id DESC");
        $st->execute([$clienteId]);
        return $st->fetchAll();
    }

    public static function buscarDoCliente(int $clienteId, int $pedidoId): ?array {
        $pdo = BD::conn();
        $st = $pdo->prepare("SELECT * FROM pedido WHERE id=? AND cliente_id=?");
        $st->execute([$pedidoId, $clienteId]);
        $pedido = $st->fetch();
        if (!$pedido) return null;

        $it = $pdo->prepare("
            SELECT i.*, p.nome, p.imagem, u.sigla AS unidade_sigla
            FROM item_pedido i
            JOIN produto p ON p.id = i.produto_id
            JOIN unidade u ON u.id = p.unidade_id
            WHERE i.pedido_id=?
        ");
        $it->execute([$pedidoId]);
        $pedido['itens'] = $it->fetchAll();
        return $pedido;
    }
}
