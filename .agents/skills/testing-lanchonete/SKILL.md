---
name: testing-lanchonete
description: End-to-end UI testing of the Lanchonete do Shopping ordering platform (guest menu/cart/checkout/PIX + admin panel). Use when verifying customer-order or admin changes in this Laravel app.
---

# Testing the Lanchonete do Shopping app

Laravel 12 + Blade + Alpine.js + Tailwind + MySQL snack-bar ordering platform.
Public guest ordering flow + admin panel at `/admin`.

## Run it locally
```bash
sudo service mysql start
php artisan migrate:fresh --seed --force   # clean state so dashboard KPIs reflect only your test order
npm run build                              # if assets changed (Node 20.18 shows a Vite warning but build succeeds)
php artisan serve --host=127.0.0.1 --port=8000
```
App: http://127.0.0.1:8000 — Admin: http://127.0.0.1:8000/admin/login

Tip: reset the DB before a recorded run so admin KPIs (Faturamento/Pedidos hoje/Ticket) map 1:1 to the order you place — this makes assertions unambiguous.

## Credentials (local dev only)
- Admin seed: `admin@lanchonete.test` / `password`
- DB: `lanchonete` / `lanchonete` @ 127.0.0.1:3306

## Payments
- Default `PAYMENT_DRIVER=mock` — no secrets needed. PIX QR + copia-e-cola are really generated; PIX stays pending until the demo "Já efetuei o pagamento" button. Card approves unless the number ends in `0` (ends-in-0 = simulated decline). Card data is not persisted.
- Real Mercado Pago/Stripe network calls are NOT implemented — do not claim real settlement works.

## Golden-path E2E flow (customer)
1. `/cardapio` → search filters products (e.g. "bacon").
2. Open a product → check an add-on + set qty; the Alpine **live total** = (base + addon) × qty. This is the key add-on assertion.
3. "Adicionar ao carrinho" → cart shows unit price incl. add-on, qty, subtotal.
4. Checkout (no login): fill name/phone/mesa/notes, pick PIX → payment page with QR + copia-e-cola.
5. Click "Já efetuei o pagamento" → confirmation shows payment = **pago**.
6. `/acompanhar` → search by the **phone** used → order found (tracking without login).

## Golden-path (admin)
1. Login → dashboard KPIs should reflect the paid order (Faturamento, Pedidos hoje, Ticket médio, top product).
2. Pedidos → open the order → change status via the `status` <select> (Novo→Em preparo) → "Salvar status"; a "Status do pedido atualizado." flash appears and the badge persists in both detail and the list.

## Notes / gotchas
- Route model binding: products by slug, orders by `order_number` (e.g. `P2607213594`), categories by slug.
- Order numbers look like `P<yymmdd><seq>`.
- Admin uses a separate `admin` guard (table `admins`), distinct from customer `users`.
- The add-on checkbox submits the add-on **id** (`name="addons[]" value="{id}"`) while Alpine tracks price separately — if the live total shows base price only, that binding is broken.

## Devin Secrets Needed
None. Fully local with mock payments; no external credentials required.
