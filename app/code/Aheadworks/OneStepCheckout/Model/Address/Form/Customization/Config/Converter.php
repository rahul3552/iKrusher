<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Address\Form\Customization\Config;

use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Stdlib\BooleanUtils;

/**
 * Class Converter
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\Customization\Config
 */
class Converter implements ConverterInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(BooleanUtils $booleanUtils)
    {
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $output = [];

        /** @var \DOMNodeList $addresses */
        $addresses = $source->getElementsByTagName('address');
        /** @var \DOMNode $address */
        foreach ($addresses as $address) {
            $addressConfig = [];
            $addressType = $address->attributes->getNamedItem('type')->nodeValue;
            foreach ($address->getElementsByTagName('attribute') as $attribute) {
                $attributeCode = $attribute->attributes->getNamedItem('code')->nodeValue;

                $metaEditPermissions = [];
                $items = $attribute->getElementsByTagName('item');
                if ($items->length > 0) {
                    foreach ($items as $item) {
                        $index = $item->attributes->getNamedItem('index')->nodeValue;
                        $metaEditPermissions[$index] = $this->getMetaEditPermissions($item);
                    }
                } else {
                    $metaEditPermissions = $this->getMetaEditPermissions($attribute);
                }
                $addressConfig[$attributeCode] = $metaEditPermissions;
            }
            $output[$addressType] = $addressConfig;
        }

        return $output;
    }

    /**
     * Get meta edit permissions config
     *
     * @param \DOMNode $parentNode
     * @return array
     */
    private function getMetaEditPermissions($parentNode)
    {
        $metaEditPermissions = [];
        foreach ($parentNode->getElementsByTagName('metadata') as $metadata) {
            foreach ($metadata->getElementsByTagName('field') as $field) {
                $fieldName = $field->attributes->getNamedItem('name')->nodeValue;
                foreach ($field->getElementsByTagName('editable') as $editable) {
                    $metaEditPermissions[$fieldName] = $this->booleanUtils->toBoolean($editable->nodeValue);
                }
            }
        }
        return $metaEditPermissions;
    }
}
