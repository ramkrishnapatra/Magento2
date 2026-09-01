## 🚀 Key Tasks & Features

* **Dynamic Configurable Stock Display:** Injects child product stock quantities directly into the frontend swatch JSON config via a plugin on `Magento\ConfigurableProduct\Block\Product\View\Type\Configurable`.
* **Custom Pricing Box & Discount Badge:** Extends price rendering to display green final prices, custom percentage badges (e.g., `20% OFF`), and strike-through regular prices.
* **Customer Activity Logger:** Preference on `AccountManagementInterface` to record `MANUAL_LOGIN` and `REGISTRATION_AUTO_LOGIN` events into `var/log/customer_login_activity.log`.
* **Guest Checkout Gatekeeper:** Observer on `controller_action_predispatch_checkout_index_index` that restricts guest checkouts for cart values under 500.

---

## 📂 Module Architecture

```text
app/code/Codilar/StockQty/
├── Block/
│   └── FinalPriceBox.php                                  # Price box calculations & discount percent logic
├── Model/
│   └── AccountManagement.php                              # Preference to log login & registration activity
├── Observer/
│   └── CheckGuestCartTotal.php                            # Predispatch checkout restriction observer
├── Plugin/
│   └── ConfigurableProduct/
│       └── Block/
│           └── Product/
│               └── View/
│                   └── Type/
│                       └── ConfigurablePlugin.php         # Plugin adding child stock qty to jsonConfig
├── etc/
│   ├── di.xml                                             # Dependency injection, plugins & preferences
│   ├── events.xml                                         # Checkout predispatch event declaration
│   └── module.xml                                         # Module declaration & sequence dependencies
├── view/
│   ├── base/
│   │   └── layout/
│   │       └── catalog_product_prices.xml                 # Replaces default price rendering templates
│   └── frontend/
│       ├── layout/
│       │   └── catalog_product_view.xml                   # Injects stock display block into PDP
│       ├── templates/
│       │   ├── final_price.phtml                          # Custom discount price HTML wrapper
│       │   └── stock_qty.phtml                            # Dynamic swatch stock quantity JS & template
│       └── web/
│           └── css/
│               └── style.css                              # Pricing, badge, and strike-through styles
├── registration.php                                       # Magento component registration
