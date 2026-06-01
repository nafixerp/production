# API Documentation

Base URL: `/api/v1`

## Auth
- POST `/auth/login`
- POST `/auth/logout`
- GET `/auth/me`

## Masters
- GET/POST `/raw-materials`
- GET/POST `/finished-goods`
- GET/POST `/recipes`
- GET/POST `/suppliers`
- GET/POST `/customers`
- GET/POST `/staff`

## Purchase
- POST `/purchase-requisitions`
- POST `/purchase-orders`
- POST `/grn`
- POST `/purchase-invoices`
- POST `/purchase-returns`

## Inventory
- GET `/stock-ledger`
- GET `/batches`
- GET `/expiry-stock`
- POST `/stock-transfer`
- POST `/stock-adjustment`
- POST `/damage-writeoff`

## Production
- POST `/production-plans`
- POST `/production-orders`
- POST `/material-issue`
- POST `/stage-entry`
- POST `/finished-goods-receipt`
- GET `/production-costing/{id}`

## Quality
- POST `/incoming-qc`
- POST `/process-qc`
- POST `/final-qc`
- GET `/batch-trace/{batch_no}`
- POST `/recall`

## Sales
- POST `/sales-orders`
- POST `/sales-invoices`
- POST `/sales-returns`
- POST `/delivery-notes`

## E-Commerce
- GET `/store/products`
- POST `/cart`
- POST `/checkout`
- GET `/online-orders`
- POST `/online-returns`

## Accounts
- POST `/receipts`
- POST `/payments`
- POST `/journals`
- GET `/ledger/{account_id}`
- GET `/trial-balance`
- GET `/profit-loss`
- GET `/balance-sheet`
