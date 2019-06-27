<?php
/**
 * Copyright (c) 2017 H&O E-commerce specialisten B.V. (http://www.h-o.nl/)
 * See LICENSE.txt for license details.
 */
namespace Paazl\Shipping\Model\Ui\Component\Layout;

use Magento\Framework\View\Element\UiComponentInterface;

class Tabs extends \Magento\Ui\Component\Layout\Tabs
{
    /**
     * To prepare the structure of child components
     *
     * @param UiComponentInterface $component
     * @param string $parentName
     * @return array
     */
    protected function prepareChildComponents(UiComponentInterface $component, $parentName)
    {
        // Remove old street from Admin -> customer -> addresses
        if (
            in_array($this->component->getComponentName(), ['customer_form', ])
            && in_array($this->component->getName(), array('street', 'street_0', 'street_1', ))
        ) {
            return [$component, []];
        }

        return parent::prepareChildComponents($component, $parentName);
    }
}
