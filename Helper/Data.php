<?php
namespace Experius\ApiExtend\Helper;

use Magento\Framework\DataObject;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
    public function getModuleConfig($field = false, $group = false, $section = false)
    {
        $section = ($section) ? $section : 'webapi';
        $group = ($group) ?  $group : 'experius_api_extend';
        $field = ($field) ? $field : 'enabled';
        return $this->scopeConfig->getValue("{$section}/{$group}/{$field}", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSalesOrderCustomerAttributes()
    {
        $group = "experius_api_extend/sales_order";
        return explode(',', $this->getModuleConfig("customer_attributes", $group));
    }
}
