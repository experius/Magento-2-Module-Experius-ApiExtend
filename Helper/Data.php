<?php
/**
 * A Magento 2 module named Experius/ApiExtend
 * Copyright (C) 2017 Lewis Voncken
 *
 * This file included in Experius/ApiExtend is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Experius\ApiExtend\Helper;

use Magento\Framework\DataObject;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * @param bool $field
     * @param bool $group
     * @param bool $section
     * @return mixed
     */
    public function getModuleConfig($field = false, $group = false, $section = false)
    {
        $section = ($section) ? $section : 'webapi';
        $group = ($group) ?  $group : 'experius_api_extend';
        $field = ($field) ? $field : 'enabled';
        return $this->scopeConfig->getValue("{$section}/{$group}/{$field}", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getSalesOrderCustomerAttributes()
    {
        $group = "experius_api_extend/sales_order";
        return explode(',', $this->getModuleConfig("customer_attributes", $group));
    }

    /**
     * @return mixed|string
     */
    public function getParamStore()
    {
        return ($this->request->getParam('store')) ? $this->request->getParam('store') : 'all';
    }
}
