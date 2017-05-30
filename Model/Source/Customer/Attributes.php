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

namespace Experius\ApiExtend\Model\Source\Customer;


class Attributes extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

    /**
     * @var \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory
     */
    protected $attributesFactory;

    /**
     * Attributes constructor.
     * @param \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributesFactory
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributesFactory
    ) {
        $this->attributesFactory = $attributesFactory;
    }

    /**
     * @return array
     */
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