## 🌟 Key Features

* **FIFO Queue Management:** Submissions are ordered chronologically (`created_at ASC`) to ensure first-come, first-served enquiry handling.
* **Service Contract Architecture:** Fully decoupled persistence layer adhering to Magento 2 repository design patterns.
* **Status Workflow:** Real-time toggling between `pending` and `handled` states directly from the queue interface.
* **Declarative Schema Support:** Native database definition using `db_schema.xml` and `db_schema_whitelist.json`.
* **Frontend Navigation:** Integrated navigation button template injected globally via layout XML.

---

## 📁 Project Directory Structure

```text
BookingEnquiry/
├── Api/
│   ├── Data/
│   │   └── BookingEnquiryInterface.php
│   └── BookingEnquiryRepositoryInterface.php
├── Block/
│   └── Listing.php
├── Controller/
│   └── Index/
│       ├── Form.php
│       ├── Listing.php
│       ├── Save.php
│       └── UpdateStatus.php
├── etc/
│   ├── frontend/
│   │   └── routes.xml
│   ├── db_schema_whitelist.json
│   ├── db_schema.xml
│   ├── di.xml
│   └── module.xml
├── Model/
│   ├── ResourceModel/
│   │   ├── BookingEnquiry/
│   │   │   └── Collection.php
│   │   └── BookingEnquiry.php
│   ├── BookingEnquiry.php
│   └── BookingEnquiryRepository.php
├── view/
│   └── frontend/
│       ├── layout/
│       │   ├── booking_index_form.xml
│       │   ├── booking_index_listing.xml
│       │   └── default.xml
│       └── templates/
│           ├── form.phtml
│           ├── list.phtml
│           └── nav_button.phtml
└── registration.php
