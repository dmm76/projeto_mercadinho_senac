<?php

declare(strict_types=1);

namespace App\Models;

use App\DAO\Database;
use PDO;

class Pedido
{
    public static function criarComItens(int $clienteId, ?int $enderecoId, string $entrega, string $pagamento, array $carrinho): int
    {
        return Database::transaction(function (PDO $pdo) use ($clienteId, $enderecoId, $entrega, $pagamento, $carrinho): int {
            $subtotal = 0.00;

            foreach ($carrinho as $pid => $item) {
                $produto = $item['produto'] ?? null;
                $quantidade = (float)($item['quantidade'] ?? 0);
                if (!is_array($produto) || $quantidade <= 0) {
                    throw new \RuntimeException('Item de carrinho invalido.');
                }

                $st = $pdo->prepare('SELECT quantidade FROM estoque WHERE produto_id = ? FOR UPDATE');
                $st->execute([$pid]);
                $estoque = $st->fetchColumn();
                if ($estoque === false || (float)$estoque < $quantidade) {
                    throw new \RuntimeException('Estoque insuficiente para o produto #' . $pid);
                }

                $subtotal += ((float)($produto['preco_atual'] ?? 0)) * $quantidade;
            }

            $frete = 0.00;
            $desconto = 0.00;
            $total = $subtotal - $desconto + $frete;

            $sqlPedido = "INSERT INTO pedido (cliente_id, endereco_id, status, entrega, pagamento, subtotal, frete, desconto, total)
                           VALUES (?, ?, 'novo', ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sqlPedido)->execute([
                $clienteId,
                $enderecoId,
                $entrega,
                $pagamento,
                $subtotal,
                $frete,
                $desconto,
                $total,
            ]);
            $pedidoId = (int)$pdo->lastInsertId();

            $sqlItem = 'INSERT INTO item_pedido (pedido_id, produto_id, quantidade, preco_unit) VALUES (?, ?, ?, ?)';
            $stItem = $pdo->prepare($sqlItem);

            $stBaixa = $pdo->prepare('UPDATE estoque SET quantidade = quantidade - ? WHERE produto_id = ?');
            $stMov = $pdo->prepare("INSERT INTO mov_estoque (produto_id, tipo, quantidade, origem, referencia_id, observacao)
                                     VALUES (?, 'saida', ?, 'pedido', ?, 'Saida por venda')");

            foreach ($carrinho as $pid => $item) {
                $produto = $item['produto'] ?? [];
                $quantidade = (float)($item['quantidade'] ?? 0);
                $preco = (float)($produto['preco_atual'] ?? 0);

                $stItem->execute([$pedidoId, $pid, $quantidade, $preco]);
                $stBaixa->execute([$quantidade, $pid]);
                $stMov->execute([$pid, $quantidade, $pedidoId]);
            }

            return $pedidoId;
        });
    }

    public static function listarDoCliente(int $clienteId): array
    {
        $pdo = Database::getConnection();
        $st = $pdo->prepare('SELECT * FROM pedido WHERE cliente_id = ? ORDER BY id DESC');
        $st->execute([$clienteId]);
        /** @var array<int, array<string, mixed>> $rows */
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $rows;
    }

    public static function buscarDoCliente(int $clienteId, int $pedidoId): ?array
    {
        $pdo = Database::getConnection();
        $st = $pdo->prepare('SELECT * FROM pedido WHERE id = ? AND cliente_id = ?');
        $st->execute([$pedidoId, $clienteId]);
        /** @var array<string, mixed>|false $pedido */
        $pedido = $st->fetch(PDO::FETCH_ASSOC);
        if ($pedido === false) {
            return null;
        }

        $it = $pdo->prepare(
            'SELECT i.*, p.nome, p.imagem, u.sigla AS unidade_sigla
             FROM item_pedido i
             JOIN produto p ON p.id = i.produto_id
             JOIN unidade u ON u.id = p.unidade_id
             WHERE i.pedido_id = ?'
        );
        $it->execute([$pedidoId]);
        $pedido['itens'] = $it->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $pedido;
    }
}
