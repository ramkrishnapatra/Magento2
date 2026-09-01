## 🚀 Key Features

* **Custom Account Navigation:** Replaces the default `My Orders` tab with a streamlined `Order Summary` dashboard link.
* **Order Analytics:** Summarizes key customer purchase metrics in a single view:
    * Total number of orders placed
    * Total amount spent across the account lifecycle
    * Date/Details of the last order placed
    * Count of successfully completed orders
    * Count of cancelled orders
* **Strict Modern Standards:** Built using `declare(strict_types=1);`, `HttpGetActionInterface`, and standard Magento UI markup patterns.
* **Security & Escaping:** All output values are secured using Magento's built-in `$block->escapeHtml()` escaping functions.

---

## 📂 Module Architecture

```text
app/code/Codilar/CustomerStats/
├── Block/
│   └── Stats.php
├── Controller/
│   └── Index/
│       └── Index.php
├── etc/
│   ├── frontend/
│   │   └── routes.xml
│   └── module.xml
├── view/
│   └── frontend/
│       ├── layout/
│       │   ├── customer_account.xml
│       │   └── customerstats_index_index.xml
│       └── templates/
│           └── stats.phtml
├── registration.php
└── README.md
