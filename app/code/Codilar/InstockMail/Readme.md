## 🌟 Features

* **Headless REST API Endpoint:** Asynchronous subscription via `/rest/V1/stock-alert/subscribe`.
* **Guest & Registered Customer Handling:** Works seamlessly for guests (ID: 0) and logged-in customer accounts.
* **Duplicate Subscription Guard:** Filters existing `pending` subscriptions by product and customer email.
* **Automated Cron Email Dispatch:** Background cron queue checks restocked products and dispatches transactional emails using custom responsive templates.
* **Declarative Schema:** Uses Magento 2's native `db_schema.xml` and `db_schema_whitelist.json`.
* **XSS Secured & Native Messages:** Protected against XSS via `$escaper` and displays messages natively through `Magento_Customer/js/customer-data`.

---

## 📁 Project Directory Structure

```text
InstockMail/
├── Api/
│   ├── Data/
│   │   └── StockAlertInterface.php
│   └── StockAlertRepositoryInterface.php
├── Cron/
│   └── SendStockAlertEmails.php
├── etc/
│   ├── cron_groups.xml
│   ├── crontab.xml
│   ├── db_schema_whitelist.json
│   ├── db_schema.xml
│   ├── di.xml
│   ├── email_templates.xml
│   ├── module.xml
│   └── webapi.xml
├── Model/
│   ├── ResourceModel/
│   │   ├── StockAlert/
│   │   │   └── Collection.php
│   │   └── StockAlert.php
│   ├── StockAlert.php
│   └── StockAlertRepository.php
├── view/
│   └── frontend/
│       ├── email/
│       │   └── custom_stock_alert.html
│       ├── layout/
│       │   └── catalog_product_view.xml
│       └── templates/
│           └── product/
│               └── view/
│                   └── type.phtml
└── registration.php
```
