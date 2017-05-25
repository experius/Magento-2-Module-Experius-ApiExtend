<?php


namespace Experius\ApiExtend\Model\Source\Customer;


class Attributes extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

    protected $attributesFactory;

    public function __construct(
        \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributesFactory
    ) {
        $this->attributesFactory = $attributesFactory;
    }

    public function getAllOptions()
    {

        $options = array();

        $attributeCollection = $this->attributesFactory->create();
        $attributeCollection->addSystemHiddenFilter()->addExcludeHiddenFrontendFilter();

        foreach ($attributeCollection as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $attributeLabel = $attribute->getFrontendLabel();
            $options[] = array('value' => $attributeCode, 'label'=> $attributeLabel);
        }

        return $options;
    }
}