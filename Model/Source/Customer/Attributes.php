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

use Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Attributes extends AbstractSource
{

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $attributesFactory;

    /**
     * Attributes constructor.
     * @param CollectionFactory $attributesFactory
     */
    public function __construct(
        CollectionFactory $attributesFactory
    ) {
        $this->attributesFactory = $attributesFactory;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $options = [];

        $attributeCollection = $this->attributesFactory->create();
        $attributeCollection->addSystemHiddenFilter()->addExcludeHiddenFrontendFilter();

        foreach ($attributeCollection as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $attributeLabel = $attribute->getFrontendLabel();
            $options[] = ['value' => $attributeCode, 'label' => $attributeLabel];
        }

        return $options;
    }
}