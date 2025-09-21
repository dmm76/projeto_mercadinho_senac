# Mercadinho Borba Gato

Aplicação web em PHP 8 para gestão de um mercadinho de bairro com vitrine online e painel administrativo. O projeto foi modernizado mantendo o legado de telas HTML e evoluindo para um MVC leve com rotas próprias.

## Visão Geral

- **Frontend**: páginas públicas com Bootstrap 5 (home, produtos, carrinho, checkout, contato) + portal do cliente (`/conta`)
- **Backend**: PHP puro com namespace `App\`, roteador simples e controllers separados para site, conta e admin
- **Admin**: dashboard completo com gerenciamento de produtos, categorias, marcas, unidades, usuários, pedidos e mensagens de contato
- **Banco de dados**: MySQL 8 (dump e scripts em `/banco`)

## Requisitos

- PHP 8.0+
- MySQL 8+
- Composer

## Instalação Rápida

```bash
cp .env.example .env
composer install
# Ajuste variáveis do .env conforme o seu ambiente
# Importe o dump principal do banco
mysql -u root -p mercadinho < banco/mercadinho.sql

```

Configure o virtual host ou acesse via `http://localhost/projeto_mercadinho_senac/public`.

## Estrutura

```
App/
  Controllers/
    Site/  -> páginas públicas (home, produtos, carrinho, checkout, contato)
    Conta/ -> portal do cliente (dash, pedidos, dados, endereços)
    Admin/ -> painel administrativo
  Core/        -> Router, View, Auth, helpers
  DAO/         -> Data Access Objects (Database, ProdutoDAO, MensagemDAO, ...)
  Models/      -> Modelos de domínio utilizados no site/conta
  Views/       -> Templates PHP com Bootstrap e partials compartilhados
```

## Funcionalidades Principais

- Cadastro e login de clientes com vínculo automático ao carrinho/pedidos
- Listagem de produtos com integração a estoque, preços promocionais e carrinho
- Checkout completo com seleção de endereço, forma de entrega e pagamento
- Portal do cliente com histórico de pedidos, dados pessoais e endereços
- Painel admin atualizado com:
  - Dashboard e KPIs
  - CRUD de produtos/categorias/marcas/unidades
  - Gestão de usuários do sistema
  - Pedidos em tempo real com filtros (status, datas, busca)
  - Mensagens de contato centralizadas
  - Tela de configurações para dados da loja e checkout

## Scripts Úteis

- `composer test` – executa a suíte PHPUnit
- `composer stan` – análise estática (phpstan)
- `composer cs` / `composer fix` – valida e aplica PSR-12 com PHP-CS-Fixer

## Roadmap

- Persistir configurações (tabela `configuracao`) e habilitar o formulário do admin
- Implementar ações nos painéis (ativar/desativar usuários, responder mensagens, atualizar status de pedidos)
- Criar painel de caixa/compras usando as tabelas existentes no banco

---

Projeto mantido pela equipe Mercadinho Borba Gato. Contribuições e sugestões são bem-vindas!

## Licenciamento

Este projeto adota licenciamento duplo:

- **Não comercial**: PolyForm Noncommercial 1.0.0 (ver `LICENSE`).
- **Comercial**: disponível mediante acordo e royalties (ver `COMMERCIAL-LICENSE.md`).

Para licenças comerciais, entre em contato: seuemail@exemplo.com.
