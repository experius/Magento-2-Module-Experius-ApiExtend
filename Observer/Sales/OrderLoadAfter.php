<?php


namespace Experius\ApiExtend\Observer\Sales;

class OrderLoadAfter implements \Magento\Framework\Event\ObserverInterface
{

    protected $_customerRepositoryInterface;

    protected $helper;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Experius\ApiExtend\Helper\Data $helper
    ) {
        $this->helper = $helper;
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
                foreach ($customerAttributes as $attribute) {
                    if (isset($attribute) && $attribute) {
                        if ($customer->getCustomAttribute($attribute)) {
                            $value = $customer->getCustomAttribute($attribute)->getValue();
                        }
                        $value = (isset($value)) ? $value : '';
                        $extensionAttributes->setData('customer_' . $attribute, $value);
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
