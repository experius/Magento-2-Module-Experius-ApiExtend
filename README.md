Mage2 Module Experius ApiExtend
====================

Extend the API of Magento 2

   ``experius/module-apiextend``
   
 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Change log](#markdown-header-change-log)

# Main Functionalities

 - Make it possible to dynamically add Customer Data to the salesOrderRepository based on configuration
   Before configuration please update the extension_attributes.xml for you customer attributes

 
 ---

# Change log

Version 1.3.0 - Oct. 27, 2020 | Lewis Voncken

 * Removed the swagger template overwrite which is now default Magento
 * Removed the setup_version tag from the module.xml

---

Version 1.2.1 - Aug. 29, 2017 | Lewis Voncken

 * Changed logic to get the Customer Attribute Label

---

Version 1.2.0 - Jun. 2, 2017 | Lewis Voncken

 * Moved logic to Plugin to also cover getList of Orders and only enable in webapi_rest

---

Version 1.1.0 - May. 30, 2017 | Lewis Voncken

 * Added License and add functionality to pass store code as param in Swagger
	example: ?store=default
   If param is not set all is used

---


Version 1.0.1 - May. 26, 2017 | Lewis Voncken

 * Get Option Value label for multiselect and select Customer Attribute

---

Version 1.0.0 - May. 25, 2017 | Lewis Voncken

 * Initial Commit

---
