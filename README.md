# Almoço do Shopping

Plataforma web de pedidos de **almoço** para a praça de alimentação. O cliente acessa o
cardápio do dia, monta o pedido e paga **sem precisar criar conta**; a cozinha gerencia
tudo por um painel administrativo.

Não há lanches, combos ou porções: o foco é exclusivamente almoço, com os pratos do dia
cadastrados e publicados pelo administrador.

## Stack

- **Laravel 12** (Eloquent ORM, Laravel Sanctum)
- **Blade + Alpine.js** no front-end
- **Tailwind CSS v4** (via Vite)
- **MySQL**
- Pagamento **PIX / Cartão** com camada de gateway plugável (driver `mock` por padrão,
  estrutura pronta para Mercado Pago)

## Funcionalidades

**Cliente (público):** home com o almoço do dia, cardápio, busca, detalhe do prato com
adicionais e quantidade, carrinho, checkout sem login (nome, telefone, mesa/local,
observações), pagamento (PIX com QR Code + copia-e-cola, ou cartão), confirmação e
acompanhamento do pedido por número do pedido ou telefone.

**Admin (`/admin`):** dashboard com indicadores (faturamento, pedidos do dia, ticket médio,
pratos mais vendidos), **cardápio do dia** (seleção diária de pratos, marcação de
esgotado e publicação), CRUD de pratos, categorias e adicionais, controle de estoque
opcional, gestão de pedidos por status (novo → em preparo → pronto → entregue / cancelado),
relatórios por período, cadastro de administradores e configurações da loja.

## Como rodar

```bash
composer install
cp .env.example .env
php artisan key:generate

# Configure o MySQL no .env (DB_DATABASE / DB_USERNAME / DB_PASSWORD)
php artisan migrate --seed

# opcional: pratos de exemplo + cardápio publicado para hoje
php artisan db:seed --class=DemoDishesSeeder

npm install
npm run build      # ou: npm run dev

php artisan serve
```

Acesse `http://localhost:8000`.

### Credenciais do admin (seed)

- **E-mail:** `admin@lanchonete.test`
- **Senha:** `password`

## Pagamentos

O driver padrão é `mock` (`PAYMENT_DRIVER=mock`), que não requer credenciais: gera um
código PIX (BR Code) válido com QR Code e simula a confirmação, e aprova cartões
instantaneamente (números terminados em `0` são recusados, para testar a falha).

Para integrar um provedor real (Mercado Pago), preencha `MERCADOPAGO_ACCESS_TOKEN` no
`.env`, mude `PAYMENT_DRIVER=mercadopago` e implemente as chamadas em
`app/Services/Payments/MercadoPagoGateway.php` (a interface e o wiring já existem).

## Testes

```bash
php artisan test
```
