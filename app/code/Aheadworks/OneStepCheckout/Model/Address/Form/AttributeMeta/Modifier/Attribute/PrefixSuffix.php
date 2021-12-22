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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Escaper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class PrefixSuffix
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute
 */
class PrefixSuffix implements ModifierInterface
{
    /**
     * @var string
     */
    private $attributeCode;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Escaper $escaper
     * @param string $attributeCode
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Escaper $escaper,
        $attributeCode
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->escaper = $escaper;
        $this->attributeCode = $attributeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function modify($metadata, $addressType)
    {
        $attributeOptionsValue = (string)$this->scopeConfig->getValue(
            'customer/address/' . $this->attributeCode . '_options',
            ScopeInterface::SCOPE_STORE
        );
        $attributeOptionsValue = trim($attributeOptionsValue);
        if (!empty($attributeOptionsValue)) {
            $metadata['dataType'] = 'select';
            $metadata['formElement'] = 'select';

            $options = [];
            $attributeOptions = explode(';', $attributeOptionsValue);
            foreach ($attributeOptions as $attributeOption) {
                $value = $this->escaper->escapeHtml(trim($attributeOption));
                $options[] = ['value' => $value, 'label' => $value];
            }
            $metadata['options'] = $options;
        }
        $metadata['system'] = true;
        return $metadata;
    }
}
