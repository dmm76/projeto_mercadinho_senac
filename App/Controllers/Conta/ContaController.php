<?php

declare(strict_types=1);

namespace App\Controllers\Conta;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Url;
use App\DAO\Database;
use App\Core\Csrf;
use PDO;

final class ContaController extends BaseContaController
{
    public function dashboard(): void
    {
        $pdo = Database::getConnection();
        $u = Auth::user();
        $clienteId = Auth::clienteId();

        $totalPedidos = 0;
        $qtdEnderecos = 0;

        // Contagem do carrinho considera cada item unico com quantidade positiva
        $cart = $_SESSION['carrinho'] ?? [];
        $cartCount = 0;
        if (is_array($cart)) {
            foreach ($cart as $item) {
                $qty = $item['quantidade'] ?? null;
                if (is_numeric($qty) && (float) $qty <= 0) {
                    continue;
                }
                $cartCount++;
            }
        }

        $ultimosPedidos = [];

        if ($clienteId) {
            $st1 = $pdo->prepare('SELECT COUNT(*) FROM pedido WHERE cliente_id = ?');
            $st1->execute([$clienteId]);
            $totalPedidos = (int)$st1->fetchColumn();

            $st2 = $pdo->prepare('SELECT id, codigo_externo AS codigo, status, total, criado_em
                                  FROM pedido
                                  WHERE cliente_id = ?
                                  ORDER BY id DESC
                                  LIMIT 5');
            $st2->execute([$clienteId]);
            $ultimosPedidos = $st2->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $st3 = $pdo->prepare('SELECT COUNT(*) FROM endereco WHERE cliente_id = ?');
            $st3->execute([$clienteId]);
            $qtdEnderecos = (int)$st3->fetchColumn();
        }

        $this->render('conta/dashboard', [
            'user'           => $u,
            'totalPedidos'   => $totalPedidos,
            'qtdEnderecos'   => $qtdEnderecos,
            'cartCount'      => $cartCount,
            'ultimosPedidos' => $ultimosPedidos,
        ]);
    }

    /* ========= PEDIDOS ========= */

    // LISTA
    public function pedidos(): void
    {
        $pdo = Database::getConnection();
        $clienteId = $this->clienteIdOrFail();

        $st = $pdo->prepare('SELECT id, codigo_externo AS codigo, status, total, criado_em
                             FROM pedido
                             WHERE cliente_id = ?
                             ORDER BY id DESC');
        $st->execute([$clienteId]);
        $pedidos = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->render('conta/pedidos/index', compact('pedidos'));
    }

    public function verPedidoQuery(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            Flash::set('error', 'Pedido inv??lido.');
            $this->redirect('/conta/pedidos');
        }
        $this->verPedido($id);
    }

    // DETALHE (GET /conta/pedidos/{id})
    public function verPedido(int $id): void
    {
        $pdo = Database::getConnection();
        $clienteId = $this->clienteIdOrFail();

        // Cabe????alho do pedido
        $cab = $pdo->prepare(
            'SELECT id,
                    codigo_externo AS codigo,
                    status, entrega, pagamento,
                    subtotal, frete, desconto, total, criado_em
               FROM pedido
              WHERE id = ? AND cliente_id = ?
              LIMIT 1'
        );
        $cab->execute([$id, $clienteId]);
        $pedido = $cab->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            Flash::set('error', 'Pedido n??o encontrado.');
            $this->redirect('/conta/pedidos');
        }

        // Itens do pedido
        $sti = $pdo->prepare('
            SELECT ip.produto_id,
                   p.nome,
                   ip.quantidade,
                   ip.preco_unit AS preco,
                   (ip.quantidade * ip.preco_unit) AS subtotal
              FROM item_pedido ip
              JOIN produto p ON p.id = ip.produto_id
             WHERE ip.pedido_id = ?
          ORDER BY ip.id ASC
        ');
        $sti->execute([$id]);
        $itens = $sti->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->render('conta/pedidos/ver', compact('pedido', 'itens'));
    }

    public function notaPedidoQuery(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            Flash::set('error', 'Pedido inv??lido.');
            $this->redirect('/conta/pedidos');
        }
        $this->notaPedido($id);
    }

    public function notaPedido(int $id): void
    {
        $pdo = Database::getConnection();
        $clienteId = $this->clienteIdOrFail();

        $stmt = $pdo->prepare('SELECT p.id,
                                        p.codigo_externo AS codigo,
                                        p.status,
                                        p.pagamento,
                                        p.subtotal,
                                        p.frete,
                                        p.desconto,
                                        p.total,
                                        p.entrega,
                                        p.criado_em,
                                        u.nome AS cliente_nome,
                                        u.email AS cliente_email,
                                        c.telefone AS cliente_telefone,
                                        c.cpf AS cliente_cpf  
                                   FROM pedido p
                              LEFT JOIN cliente c ON c.id = p.cliente_id
                              LEFT JOIN usuario u ON u.id = c.usuario_id
                                  WHERE p.id = ? AND p.cliente_id = ?
                                  LIMIT 1');
        $stmt->execute([$id, $clienteId]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            Flash::set('error', 'Pedido nao encontrado.');
            $this->redirect('/conta/pedidos');
        }

        $itemsStmt = $pdo->prepare('SELECT ip.produto_id,
                                              p.nome,
                                              ip.quantidade,
                                              ip.preco_unit AS preco,
                                              (ip.quantidade * ip.preco_unit) AS subtotal
                                         FROM item_pedido ip
                                         JOIN produto p ON p.id = ip.produto_id
                                        WHERE ip.pedido_id = ?
                                     ORDER BY ip.id ASC');
        $itemsStmt->execute([$id]);
        $itens = $itemsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $statusInfo = $this->formatPedidoStatus((string)($pedido['status'] ?? ''));

        $user = Auth::user();
        $cliente = [
            'nome' => $pedido['cliente_nome'] ?? ($user['nome'] ?? 'Cliente'),
            'email' => $pedido['cliente_email'] ?? ($user['email'] ?? ''),
            'telefone' => $pedido['cliente_telefone'] ?? null,
            'cpf'      => $pedido['cliente_cpf'] ?? null,
        ];

        $entregaLinhas = $this->formatEnderecoEntrega($pedido['entrega'] ?? null);

        unset($pedido['cliente_nome'], $pedido['cliente_email'], $pedido['cliente_telefone'], $pedido['cliente_cpf']);

        $empresa = [
            'nome' => 'Mercadinho Borba Gato',
            'telefone' => '(44) 3259-1533',
            'endereco' => 'R. das Tipuanas, 250, Borba Gato - Maringa - PR, 87060-130',
            'email' => 'contato@mercadinho.local',
        ];

        $this->render('conta/pedidos/nota', [
            'pedido' => $pedido,
            'itens' => $itens,
            'statusInfo' => $statusInfo,
            'cliente' => $cliente,
            'entregaLinhas' => $entregaLinhas,
            'empresa' => $empresa,
        ]);
    }

    private function formatPedidoStatus(string $status): array
    {
        $key = strtolower(trim($status));
        $map = [
            'pendente' => 'bg-warning text-dark',
            'aguardando_pagamento' => 'bg-warning text-dark',
            'aguardando' => 'bg-warning text-dark',
            'pago' => 'bg-success text-white',
            'enviado' => 'bg-primary text-white',
            'em_transporte' => 'bg-info text-dark',
            'transporte' => 'bg-info text-dark',
            'em_preparo' => 'bg-info text-dark',
            'preparando' => 'bg-info text-dark',
            'em_andamento' => 'bg-info text-dark',
            'pronto' => 'bg-secondary text-white',
            'entregue' => 'bg-success text-white',
            'finalizado' => 'bg-success text-white',
            'cancelado' => 'bg-danger text-white',
            'novo' => 'bg-secondary text-white',
        ];
        $class = $map[$key] ?? 'bg-secondary text-white';

        $label = $status !== '' ? $status : 'pendente';
        $label = str_replace(['_', '-'], ' ', $label);
        if (function_exists('mb_convert_case')) {
            $label = mb_convert_case($label, MB_CASE_TITLE, 'UTF-8');
        } else {
            $label = ucwords(strtolower($label));
        }

        return ['class' => $class, 'label' => $label];
    }

    /**
     * @return string[]
     */
    private function formatEnderecoEntrega(?string $raw): array
    {
        if ($raw === null) {
            return [];
        }
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [$raw];
        }

        $lines = [];
        $get = static function (string $key) use ($decoded): ?string {
            if (!array_key_exists($key, $decoded)) {
                return null;
            }
            $value = $decoded[$key];
            if (!is_scalar($value)) {
                return null;
            }
            $str = trim((string) $value);
            return $str !== '' ? $str : null;
        };

        $name = $get('nome') ?? $get('destinatario');
        if ($name) {
            $lines[] = $name;
        }

        $logradouro = $get('logradouro');
        $numero = $get('numero');
        $complemento = $get('complemento');
        if ($logradouro) {
            $line = $logradouro;
            if ($numero) {
                $line .= ', ' . $numero;
            }
            if ($complemento) {
                $line .= ' - ' . $complemento;
            }
            $lines[] = $line;
        }

        $bairro = $get('bairro');
        $cidade = $get('cidade');
        $uf = $get('uf');
        $cityLine = '';
        if ($bairro) {
            $cityLine .= $bairro;
        }
        if ($cidade) {
            $cityLine .= ($cityLine !== '' ? ', ' : '') . $cidade;
        }
        if ($uf) {
            $cityLine .= ($cityLine !== '' ? ' - ' : '') . strtoupper($uf);
        }
        if ($cityLine !== '') {
            $lines[] = $cityLine;
        }

        $cep = $get('cep');
        if ($cep) {
            $lines[] = 'CEP: ' . $cep;
        }

        $telefone = $get('telefone') ?? $get('fone');
        if ($telefone) {
            $lines[] = 'Telefone: ' . $telefone;
        }

        if (empty($lines)) {
            foreach ($decoded as $key => $value) {
                if (!is_scalar($value)) {
                    continue;
                }
                $valueStr = trim((string) $value);
                if ($valueStr === '') {
                    continue;
                }
                $label = str_replace(['_', '-'], ' ', (string) $key);
                $lines[] = ucfirst($label) . ': ' . $valueStr;
            }
        }

        return $lines;
    }


    /* ========= DADOS DO CLIENTE ========= */

    public function dados(): void
    {
        $pdo = Database::getConnection();
        $u = Auth::user();

        $cliente = [];
        $clienteId = Auth::clienteId(); // j?? cria se n??o existir (conforme sua Auth)
        if ($clienteId) {
            $st = $pdo->prepare('SELECT telefone, cpf, nascimento FROM cliente WHERE id = ?');
            $st->execute([$clienteId]);
            $cliente = $st->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        $this->render('conta/dados/index', [
            'user'         => $u,
            'cliente'      => $cliente,
            'perfilAction' => Url::to('/conta/dados/perfil'),
            'senhaAction'  => Url::to('/conta/dados/senha'),
        ]);
    }

    private function idFromRequest(): int
    {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id <= 0) {
            Flash::set('error', 'ID inv??lido.');
            $this->redirect('/conta/enderecos');
            exit;
        }
        return $id;
    }

    /* ========= ENDERE??OS ========= */

    public function enderecos(): void
    {
        $pdo = Database::getConnection();
        $clienteId = $this->clienteIdOrFail();

        $stmt = $pdo->prepare(
            'SELECT id, rotulo, nome, cep, logradouro, numero, complemento, bairro, cidade, uf, principal
               FROM endereco
              WHERE cliente_id = ?
           ORDER BY principal DESC, id DESC'
        );
        $stmt->execute([$clienteId]);
        $enderecos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->render('conta/enderecos/index', compact('enderecos'));
    }

    public function novoEndereco(): void
    {
        $this->render('conta/enderecos/form', [
            'isEdit'    => false,
            'endereco'  => [],
            'actionUrl' => '/conta/enderecos/novo',
        ]);
    }

    public function criarEndereco(): void
    {
        $pdo = Database::getConnection();
        $clienteId = $this->clienteIdOrFail();

        $in = $this->sanitize($_POST);
        if (!$this->validateCsrf($_POST)) return;

        [$ok, $data, $errors] = $this->validateEndereco($in);
        if (!$ok) {
            Flash::set('error', 'Verifique os campos destacados.');
            $this->render('conta/enderecos/form', [
                'isEdit'    => false,
                'endereco'  => $in,
                'errors'    => $errors,
                'actionUrl' => '/conta/enderecos/novo',
            ]);
            return;
        }

        $pdo->beginTransaction();
        try {
            if ($data['principal'] === 1) {
                $pdo->prepare('UPDATE endereco SET principal = 0 WHERE cliente_id = ?')
                    ->execute([$clienteId]);
            }

            $pdo->prepare('INSERT INTO endereco
                (cliente_id, rotulo, nome, cep, logradouro, numero, complemento, bairro, cidade, uf, principal)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')->execute([
                $clienteId,
                $data['rotulo'],
                $data['nome'],
                $data['cep'],
                $data['logradouro'],
                $data['numero'],
                $data['complemento'],
                $data['bairro'],
                $data['cidade'],
                $data['uf'],
                $data['principal'],
            ]);

            $pdo->commit();
            Flash::set('success', 'Endere??o cadastrado com sucesso.');
            $this->redirect('/conta/enderecos');
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Flash::set('error', 'Erro ao salvar endere??o.');
            $this->render('conta/enderecos/form', [
                'isEdit'    => false,
                'endereco'  => $in,
                'errors'    => ['Falha interna ao salvar.'],
                'actionUrl' => '/conta/enderecos/novo',
            ]);
        }
    }

    public function editarEnderecoQuery(): void
    {
        $this->editarEndereco($this->idFromRequest());
    }
    public function atualizarEnderecoQuery(): void
    {
        $this->atualizarEndereco($this->idFromRequest());
    }
    public function excluirEnderecoQuery(): void
    {
        $this->excluirEndereco($this->idFromRequest());
    }
    public function definirPrincipalQuery(): void
    {
        $this->definirPrincipal($this->idFromRequest());
    }

    public function editarEndereco(int $id): void
    {
        $pdo = Database::getConnection();
        $clienteId = $this->clienteIdOrFail();

        $stmt = $pdo->prepare('SELECT * FROM endereco WHERE id = ? AND cliente_id = ? LIMIT 1');
        $stmt->execute([$id, $clienteId]);
        $endereco = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$endereco) {
            Flash::set('error', 'Endere??o n??o encontrado.');
            $this->redirect('/conta/enderecos');
        }

        $this->render('conta/enderecos/form', [
            'isEdit'    => true,
            'endereco'  => $endereco,
            'actionUrl' => '/conta/enderecos/editar',
        ]);
    }

    public function atualizarEndereco(int $id): void
    {
        $pdo = Database::getConnection();
        $clienteId = $this->clienteIdOrFail();

        $in = $this->sanitize($_POST);
        if (!$this->validateCsrf($_POST)) return;

        [$ok, $data, $errors] = $this->validateEndereco($in);
        if (!$ok) {
            Flash::set('error', 'Verifique os campos destacados.');
            $in['id'] = $id;
            $this->render('conta/enderecos/form', [
                'isEdit'    => true,
                'endereco'  => $in,
                'errors'    => $errors,
                'actionUrl' => '/conta/enderecos/editar',
            ]);
            return;
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT 1 FROM endereco WHERE id = ? AND cliente_id = ?');
            $stmt->execute([$id, $clienteId]);
            if (!$stmt->fetchColumn()) {
                throw new \RuntimeException('Endere??o inv??lido.');
            }

            if ($data['principal'] === 1) {
                $pdo->prepare('UPDATE endereco SET principal = 0 WHERE cliente_id = ?')
                    ->execute([$clienteId]);
            }

            $pdo->prepare('UPDATE endereco SET
                    rotulo = ?, nome = ?, cep = ?, logradouro = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, uf = ?, principal = ?
                WHERE id = ? AND cliente_id = ?')->execute([
                $data['rotulo'],
                $data['nome'],
                $data['cep'],
                $data['logradouro'],
                $data['numero'],
                $data['complemento'],
                $data['bairro'],
                $data['cidade'],
                $data['uf'],
                $data['principal'],
                $id,
                $clienteId
            ]);

            $pdo->commit();
            Flash::set('success', 'Endere??o atualizado com sucesso.');
            $this->redirect('/conta/enderecos');
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Flash::set('error', 'Erro ao atualizar endere??o.');
            $this->redirect('/conta/enderecos');
        }
    }

    public function excluirEndereco(int $id): void
    {
        $pdo = Database::getConnection();
        $clienteId = $this->clienteIdOrFail();

        if (!$this->validateCsrf($_POST)) return;

        $check = $pdo->prepare('SELECT id FROM endereco WHERE id = ? AND cliente_id = ? LIMIT 1');
        $check->execute([$id, $clienteId]);
        if (!$check->fetch(PDO::FETCH_ASSOC)) {
            Flash::set('error', 'Endereco nao encontrado.');
            $this->redirect('/conta/enderecos');
        }

        $countStmt = $pdo->prepare('SELECT COUNT(*) FROM endereco WHERE cliente_id = ?');
        $countStmt->execute([$clienteId]);
        $total = (int)$countStmt->fetchColumn();
        if ($total <= 1) {
            Flash::set('error', 'Mantenha pelo menos um endere??o cadastrado.');
            $this->redirect('/conta/enderecos');
        }

        $pedidoStmt = $pdo->prepare("SELECT COUNT(*) FROM pedido WHERE cliente_id = ? AND endereco_id = ? AND entrega = 'entrega' AND status NOT IN ('finalizado','cancelado')");
        $pedidoStmt->execute([$clienteId, $id]);
        if ((int)$pedidoStmt->fetchColumn() > 0) {
            Flash::set('error', 'Este endereco esta vinculado a um pedido em andamento.');
            $this->redirect('/conta/enderecos');
        }

        $stmt = $pdo->prepare('DELETE FROM endereco WHERE id = ? AND cliente_id = ?');
        $ok = $stmt->execute([$id, $clienteId]);

        Flash::set($ok ? 'success' : 'error', $ok ? 'Endereco excluido.' : 'Nao foi possivel excluir.');
        $this->redirect('/conta/enderecos');
    }

    public function definirPrincipal(int $id): void
    {
        $pdo = Database::getConnection();
        $clienteId = $this->clienteIdOrFail();

        if (!$this->validateCsrf($_POST)) return;

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT 1 FROM endereco WHERE id = ? AND cliente_id = ?');
            $stmt->execute([$id, $clienteId]);
            if (!$stmt->fetchColumn()) {
                throw new \RuntimeException('Endere??o inv??lido.');
            }

            $pdo->prepare('UPDATE endereco SET principal = 0 WHERE cliente_id = ?')->execute([$clienteId]);
            $pdo->prepare('UPDATE endereco SET principal = 1 WHERE id = ? AND cliente_id = ?')->execute([$id, $clienteId]);

            $pdo->commit();
            Flash::set('success', 'Endere??o definido como principal.');
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Flash::set('error', 'N??o foi poss????vel definir como principal.');
        }
        $this->redirect('/conta/enderecos');
    }

    /* ========= HELPERS ========= */

    private function clienteIdOrFail(): int
    {
        $id = Auth::clienteId();
        if ($id !== null) return $id;
        Flash::set('error', 'Seu cadastro de cliente n??o foi localizado.');
        $this->redirect('/conta/dados');
        exit;
    }

    /** @param array<string,mixed> $src */
    private function sanitize(array $src): array
    {
        $f = fn($k, $d = '') => trim((string)($src[$k] ?? $d));
        return [
            'rotulo'      => $f('rotulo'),
            'nome'        => $f('nome'),
            'cep'         => strtoupper($f('cep')),
            'logradouro'  => $f('logradouro'),
            'numero'      => $f('numero'),
            'complemento' => $f('complemento'),
            'bairro'      => $f('bairro'),
            'cidade'      => $f('cidade'),
            'uf'          => strtoupper($f('uf')),
            'principal'   => isset($src['principal']) ? 1 : 0,
        ];
    }

    /** @return array{0:bool,1:array<string,mixed>,2:array<int,string>} */
    private function validateEndereco(array $in): array
    {
        $errors = [];
        $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];

        if ($in['nome'] === '')        $errors[] = 'Nome ?? obrigat??rio.';
        if ($in['cep'] === '')         $errors[] = 'CEP ?? obrigat??rio.';
        if ($in['logradouro'] === '')  $errors[] = 'Logradouro ?? obrigat??rio.';
        if ($in['numero'] === '')      $errors[] = 'N??mero ?? obrigat??rio.';
        if ($in['bairro'] === '')      $errors[] = 'Bairro ?? obrigat??rio.';
        if ($in['cidade'] === '')      $errors[] = 'Cidade ?? obrigat??ria.';
        if (!in_array($in['uf'], $ufs, true)) $errors[] = 'UF inv??lida.';
        if ($in['cep'] !== '' && !preg_match('/^\d{5}-?\d{3}$/', $in['cep'])) {
            $errors[] = 'CEP inv??lido (use 00000-000).';
        }
        $in['cep'] = preg_replace('/^(\d{5})-?(\d{3})$/', '$1-$2', $in['cep']);

        return [empty($errors), $in, $errors];
    }

    private function validateCsrf(array $src, string $fallbackPath = '/conta/enderecos'): bool
    {
        $token = isset($src['csrf']) ? (string)$src['csrf'] : null;
        if (!Csrf::check($token)) {
            Flash::set('error', 'Sess??o expirada. Recarregue a p??gina e tente novamente.');
            $this->redirect($fallbackPath);
            return false;
        }
        return true;
    }

    // public function salvarPerfil(): void
    // {
    //     if (!$this->validateCsrf($_POST, '/conta/dados')) return;

    //     $pdo = Database::getConnection();
    //     $u = Auth::user();
    //     $clienteId = Auth::clienteId();

    //     $nome = trim((string)($_POST['nome'] ?? ''));
    //     $tel  = trim((string)($_POST['telefone'] ?? ''));

    //     $errs = [];
    //     if ($nome === '') $errs[] = 'Informe seu nome.';
    //     if ($tel !== '' && !preg_match('/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/', $tel)) {
    //         $errs[] = 'Telefone inv????lido.';
    //     }
    //     if ($errs) {
    //         Flash::set('error', implode(' ', $errs));
    //         $this->redirect('/conta/dados');
    //         return;
    //     }

    //     $pdo->beginTransaction();
    //     try {
    //         $pdo->prepare('UPDATE usuario SET nome = ? WHERE id = ?')->execute([$nome, $u['id']]);

    //         if ($clienteId) {
    //             $pdo->prepare('UPDATE cliente SET telefone = ? WHERE id = ?')
    //                 ->execute([$tel !== '' ? $tel : null, $clienteId]);
    //         }

    //         $pdo->commit();
    //         // manter compat com navbar antiga
    //         $_SESSION['user']['nome'] = $nome;
    //         $_SESSION['nome'] = $nome;

    //         Flash::set('success', 'Dados atualizados.');
    //     } catch (\Throwable $e) {
    //         $pdo->rollBack();
    //         Flash::set('error', 'N??o foi poss??vel salvar seus dados.');
    //     }
    //     $this->redirect('/conta/dados');
    // }

    public function salvarPerfil(): void
    {
        if (!$this->validateCsrf($_POST, '/conta/dados')) return;

        $pdo = Database::getConnection();
        $u = Auth::user();
        $clienteId = Auth::clienteId();

        // Entradas do form
        $nome        = trim((string)($_POST['nome'] ?? ''));
        $tel         = trim((string)($_POST['telefone'] ?? ''));
        $cpfRaw      = trim((string)($_POST['cpf'] ?? ''));          // pode vir com m??scara
        $nascRaw     = trim((string)($_POST['nascimento'] ?? ''));   // yyyy-mm-dd (input date)

        // Normaliza????es
        $cpf = $cpfRaw !== '' ? preg_replace('/\D+/', '', $cpfRaw) : '';
        $nascimento = null;
        if ($nascRaw !== '') {
            // aceita 'Y-m-d' do input date direto
            $dt = \DateTime::createFromFormat('Y-m-d', $nascRaw);
            if ($dt instanceof \DateTime) {
                $nascimento = $dt->format('Y-m-d');
            }
        }

        // Valida????es
        $errs = [];
        if ($nome === '') {
            $errs[] = 'Informe seu nome.';
        }
        if ($tel !== '' && !preg_match('/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/', $tel)) {
            $errs[] = 'Telefone inv??lido.';
        }
        if ($cpf !== '' && !preg_match('/^\d{11}$/', $cpf)) {
            $errs[] = 'CPF inv??lido (use 11 d??gitos).';
        }
        // (Opcional) valida d??gitos verificadores do CPF:
        if ($cpf !== '' && !$this->validaCpf($cpf)) {
            $errs[] = 'CPF inv??lido.';
        }

        if ($errs) {
            Flash::set('error', implode(' ', $errs));
            $this->redirect('/conta/dados');
            return;
        }

        $pdo->beginTransaction();
        try {
            // Atualiza nome do usu??rio
            $pdo->prepare('UPDATE usuario SET nome = ? WHERE id = ?')->execute([$nome, $u['id']]);

            // Atualiza dados do cliente (telefone, cpf, nascimento)
            if ($clienteId) {
                $pdo->prepare('UPDATE cliente SET telefone = ?, cpf = ?, nascimento = ? WHERE id = ?')
                    ->execute([
                        $tel !== '' ? $tel : null,
                        $cpf !== '' ? $cpf : null,
                        $nascimento, // j?? ?? null ou 'Y-m-d'
                        $clienteId
                    ]);
            }

            $pdo->commit();

            // manter compat com navbar antiga
            $_SESSION['user']['nome'] = $nome;
            $_SESSION['nome'] = $nome;

            Flash::set('success', 'Dados atualizados.');
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Flash::set('error', 'N??o foi poss??vel salvar seus dados.');
        }
        $this->redirect('/conta/dados');
    }

    /**
     * Valida????o simples de CPF (com d??gitos verificadores).
     */
    private function validaCpf(string $cpf): bool
    {
        if (!preg_match('/^\d{11}$/', $cpf)) return false;
        if (preg_match('/^(\\d)\\1{10}$/', $cpf)) return false; // repetidos

        // c??lculo DV1
        $soma = 0;
        for ($i = 0, $peso = 10; $i < 9; $i++, $peso--) $soma += (int)$cpf[$i] * $peso;
        $resto = $soma % 11;
        $dv1 = $resto < 2 ? 0 : 11 - $resto;

        // c??lculo DV2
        $soma = 0;
        for ($i = 0, $peso = 11; $i < 10; $i++, $peso--) $soma += (int)$cpf[$i] * $peso;
        $resto = $soma % 11;
        $dv2 = $resto < 2 ? 0 : 11 - $resto;

        return ((int)$cpf[9] === $dv1) && ((int)$cpf[10] === $dv2);
    }


    public function atualizarSenha(): void
    {
        if (!$this->validateCsrf($_POST, '/conta/dados')) return;

        $pdo = Database::getConnection();
        $u = Auth::user();

        $atual = (string)($_POST['senha_atual'] ?? '');
        $s1    = (string)($_POST['senha'] ?? '');
        $s2    = (string)($_POST['senha2'] ?? '');

        if ($s1 !== $s2) {
            Flash::set('error', 'As senhas n??o conferem.');
            $this->redirect('/conta/dados');
            return;
        }
        if (strlen($s1) < 6) {
            Flash::set('error', 'A nova senha deve ter ao menos 6 caracteres.');
            $this->redirect('/conta/dados');
            return;
        }

        $st = $pdo->prepare('SELECT senha_hash FROM usuario WHERE id = ?');
        $st->execute([$u['id']]);
        $hash = (string)$st->fetchColumn();

        if (!$hash || !password_verify($atual, $hash)) {
            Flash::set('error', 'Senha atual incorreta.');
            $this->redirect('/conta/dados');
            return;
        }

        $ok = $pdo->prepare('UPDATE usuario SET senha_hash = ? WHERE id = ?')
            ->execute([password_hash($s1, PASSWORD_DEFAULT), $u['id']]);

        Flash::set($ok ? 'success' : 'error', $ok ? 'Senha atualizada.' : 'N????o foi poss????vel atualizar a senha.');
        $this->redirect('/conta/dados');
    }

    private function redirect(string $path): void
    {
        header('Location: ' . Url::to($path), true, 302);
        exit;
    }
}
