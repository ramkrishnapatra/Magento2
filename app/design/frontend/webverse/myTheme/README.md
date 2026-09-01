## 🚀 Key Features

* **Parent Inheritance:** Built cleanly on top of `Magento/luma`.
* **Dynamic Add-to-Cart Modal Popup:**
    * Subscribes directly to `Magento_Customer/js/customer-data` (`cart` section).
    * Automatically pops up with product thumbnail, title, formatted price, and direct "View Cart" action when items are added.
    * Auto-dismisses after a configurable timeout (default: 5 seconds) with manual close support.
* **Header Layout Tweaks:** Repositions the user authentication/Sign In link to the far-right end of the header navigation panel using `<move>`.
* **Theme Styling & Overrides:** Custom styles for background color, highlighted page headings, header adjustments, and modal animations.

---

## 📂 Theme Directory Structure

```text
app/design/frontend/Codilar/custom_theme/
├── Magento_Theme/
│   ├── layout/
│   │   └── default.xml                                    # Moves Sign In link to the far right
│   └── templates/
│       └── messages.phtml                                 # Message container & Add-to-Cart popup markup
├── web/
│   ├── css/
│   │   └── source/
│   │       └── _extend.less                               # Custom theme styles & popup animations
│   └── js/
│       └── cart-popup.js                                  # CustomerData cart subscription & popup logic
├── theme.xml                                              # Theme declaration & parent definition
└── registration.php                                       # Theme registration
