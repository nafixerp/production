# Food Production ERP Double Entry Accounting

## Core Rule

Every transaction posts to daybook and must balance:

Debit Total = Credit Total

## Core Accounting Tables
- account
- account_group
- account_bs_head
- daybook
- daybook_part
- receipt
- payment
- journal
- bank_recon

## Purchase Invoice

| Dr/Cr | Account | Amount |
|---|---|---:|
| Dr | Raw Material Inventory | Material Value |
| Dr | Input GST | GST |
| Cr | Supplier | Invoice Total |

## Vendor Payment

| Dr/Cr | Account | Amount |
|---|---|---:|
| Dr | Supplier | Paid Amount |
| Cr | Bank/Cash | Paid Amount |

## Material Issue to Production

| Dr/Cr | Account | Amount |
|---|---|---:|
| Dr | WIP | Material Cost |
| Cr | Raw Material Inventory | Material Cost |

## Packing Material Issue

| Dr/Cr | Account | Amount |
|---|---|---:|
| Dr | WIP | Packing Cost |
| Cr | Packing Material Inventory | Packing Cost |

## Labour Allocation

| Dr/Cr | Account | Amount |
|---|---|---:|
| Dr | WIP | Labour Cost |
| Cr | Salary Payable / Labour Recovery | Labour Cost |

## Finished Goods Receipt

| Dr/Cr | Account | Amount |
|---|---|---:|
| Dr | Finished Goods Inventory | Total Production Cost |
| Cr | WIP | Total Production Cost |

## Sales Invoice

| Dr/Cr | Account | Amount |
|---|---|---:|
| Dr | Customer/Cash/Bank | Invoice Total |
| Cr | Sales | Taxable Value |
| Cr | Output GST | GST |

## COGS Posting

| Dr/Cr | Account | Amount |
|---|---|---:|
| Dr | Cost Of Goods Sold | Cost |
| Cr | Finished Goods Inventory | Cost |

## Expired/Damaged Stock

| Dr/Cr | Account | Amount |
|---|---|---:|
| Dr | Inventory Loss / Expiry Loss | Cost |
| Cr | Inventory | Cost |

## E-Commerce Gateway Settlement

| Dr/Cr | Account | Amount |
|---|---|---:|
| Dr | Bank | Net Received |
| Dr | Payment Gateway Expense | Gateway Charges |
| Cr | Gateway Clearing / Customer | Gross Amount |
