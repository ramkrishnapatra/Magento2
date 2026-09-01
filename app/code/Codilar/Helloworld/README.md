## 🚀 Key Features

* **Custom Frontend Route:** Sets up a dedicated frontend endpoint (`/helloworld` or `/helloworld/index/index`).
* **Clean Action Controller:** Implements `Magento\Framework\App\Action\HttpGetActionInterface` with strict typing (`declare(strict_types=1);`).
* **Layout & Block Architecture:** Demonstrates decoupling of view presentation and business logic using Layout XML and custom Block classes.
* **XSS Protection:** Implements standard Magento escaping (`$block->escapeHtml()`) across all template files.

---

## 📂 Module Architecture

```text
app/code/Codilar/HelloWorld/
├── Block/
│   └── Hello.php
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
│       │   └── helloworld_index_index.xml
│       └── templates/
│           └── index.phtml
├── registration.php
└── README.md
