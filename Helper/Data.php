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

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    /**
     * @var Http
     */
    protected Http $request;

    /**
     * Data constructor.
     * @param Context $context
     * @param Http $request
     */
    public function __construct(
        Context $context,
        Http $request
    ) {
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * @param string $field
     * @param string $group
     * @param string $section
     * @return mixed
     */
    public function getModuleConfig(
        $field = 'enabled',
        $group = 'experius_api_extend',
        $section = 'webapi'
    ): mixed {
        return $this->scopeConfig->getValue("{$section}/{$group}/{$field}", ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getSalesOrderCustomerAttributes(): array
    {
        $group = "experius_api_extend/sales_order";
        if ($customerAttributes = $this->getModuleConfig("customer_attributes", $group)) {
            return explode(',', $customerAttributes);
        }
        return [];
    }

    /**
     * @return mixed|string
     */
    public function getParamStore(): mixed
    {
        return $this->request->getParam('store') ?: 'all';
    }
}
