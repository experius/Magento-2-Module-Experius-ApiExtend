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

namespace Experius\ApiExtend\Plugin\Magento\Sales\Model;

class OrderRepository
{

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Experius\ApiExtend\Helper\Data
     */
    protected $helper;

    /**
     * OrderLoadAfter constructor.
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Experius\ApiExtend\Helper\Data $helper
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Eav\Model\Config $eavConfig,
        \Experius\ApiExtend\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->eavConfig = $eavConfig;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }

    /**
     * Get gift message
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $resultOrder
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $resultOrder
    ) {
        $resultOrder = $this->getExtraExtensionAttributes($resultOrder);

        return $resultOrder;
    }

    public function getExtraExtensionAttributes($order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->getOrderExtensionDependency();
        }
        $customerId = $order->getCustomerId();
        if ($customerId) {
            $customer = $this->_customerRepositoryInterface->getById($customerId);
            $customerAttributes = $this->helper->getSalesOrderCustomerAttributes();
            if (!empty($customerAttributes)) {
                foreach ($customerAttributes as $attributeCode) {
                    if (isset($attributeCode) && $attributeCode) {
                        if ($customer->getCustomAttribute($attributeCode)) {
                            $value = $customer->getCustomAttribute($attributeCode)->getValue();
                        }
                        if (isset($value)) {
                            $attribute = $this->eavConfig->getAttribute('customer', $attributeCode);
                            if (in_array($attribute->getFrontendInput(), ['multiselect', 'select'])) {
                                $value = $attribute->getSource()->getOptionText($value);
                            }
                        }
                        $value = (isset($value)) ? $value : '';
                        $extensionAttributes->setData('customer_' . $attributeCode, $value);
                        unset($value);
                    }
                }
            }
        }
        $order->setExtensionAttributes($extensionAttributes);
        return $order;
    }

    private function getOrderExtensionDependency()
    {
        $orderExtension = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Sales\Api\Data\OrderExtension'
        );
        return $orderExtension;
    }

    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Model\ResourceModel\Order\Collection $resultOrder
    ) {
        foreach ($resultOrder->getItems() as $order) {
            $this->afterGet($subject, $order);
        }
        return $resultOrder;
    }
}