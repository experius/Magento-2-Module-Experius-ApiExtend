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

use Experius\ApiExtend\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderExtension;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;

class OrderRepository
{

    /**
     * @var CustomerRepositoryInterface
     */
    protected CustomerRepositoryInterface $_customerRepositoryInterface;

    /**
     * @var Config
     */
    protected Config $eavConfig;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * OrderLoadAfter constructor.
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Config $eavConfig
     * @param Data $helper
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        Config                      $eavConfig,
        Data                        $helper
    ) {
        $this->helper = $helper;
        $this->eavConfig = $eavConfig;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param Collection $resultOrder
     * @return Collection
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        Collection               $resultOrder
    ) {
        foreach ($resultOrder->getItems() as $order) {
            $this->afterGet($subject, $order);
        }
        return $resultOrder;
    }

    /**
     * Get gift message
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $resultOrder
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface           $resultOrder
    ) {
        $resultOrder = $this->getExtraExtensionAttributes($resultOrder);

        return $resultOrder;
    }

    /**
     * @param $order
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
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
                                foreach ($attribute->getSource()->getAllOptions() as $option) {
                                    if (isset($option['value']) && $option['value'] == $value) {
                                        $value = isset($option['label']) ? $option['label'] : $option['value'];
                                        break;
                                    }
                                }
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

    /**
     * @return OrderExtension|mixed
     */
    private function getOrderExtensionDependency()
    {
        $orderExtension = ObjectManager::getInstance()->get(
            OrderExtension::class
        );
        return $orderExtension;
    }
}