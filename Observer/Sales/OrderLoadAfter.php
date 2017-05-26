<?php


namespace Experius\ApiExtend\Observer\Sales;

class OrderLoadAfter implements \Magento\Framework\Event\ObserverInterface
{

    protected $_customerRepositoryInterface;

    protected $eavConfig;

    protected $helper;

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
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order = $observer->getOrder();
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
    }

    private function getOrderExtensionDependency()
    {
        $orderExtension = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Sales\Api\Data\OrderExtension'
        );
        return $orderExtension;
    }
}
