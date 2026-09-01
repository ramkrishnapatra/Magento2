## 🚀 Key Features

* **Registry-Free Product Retrieval:** Retrieves the active PDP product safely from layout blocks without using the deprecated `\Magento\Framework\Registry`.
* **Dynamic Category Collection:** Automatically loads visible, in-stock products matching the current product's category IDs while excluding the current item.
* **CMS Widget Integration:** Provides a custom Widget Chooser (`codilar_same_category_cms_widget`) allowing merchandisers to insert category sliders via the Admin CMS block interface.
* **Native Price & Image Rendering:** Utilizes Magento's `PricingRender` and `ImageFactory` to guarantee accurate tax/currency pricing and responsive image generation.
* **Interactive Secondary Actions:** Native support for Add to Wishlist and Add to Compare actions with `data-post` payload generation.
* **Responsive Slick Carousel:** Mobile-ready slider with breakpoints for desktop (4 items), tablet (2–3 items), and mobile (1 item).

---

## 📂 Module Architecture

```text
app/code/Codilar/SameCategory/
├── Block/
│   └── Widget/
│       ├── CmsBlockRenderer.php                           # Widget block rendering dynamic CMS blocks
│       └── SameCategory.php                               # Product collection loader, price/image/actions logic
├── etc/
│   ├── module.xml                                         # Module declaration & sequence dependencies
│   └── widget.xml                                         # Admin CMS widget configuration & block chooser
└── view/
    └── frontend/
        └── templates/
            └── widget/
                └── same_category.phtml                    # Slider HTML, CSS styling & Slick JS initialization
