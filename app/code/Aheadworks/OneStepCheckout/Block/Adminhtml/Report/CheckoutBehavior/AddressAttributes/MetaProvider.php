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
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior\AddressAttributes;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\AvailabilityChecker;
use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Framework\Stdlib\BooleanUtils;

/**
 * Class MetaProvider
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior\AddressAttributes
 */
class MetaProvider
{
    /**
     * @var AddressMetadataInterface
     */
    private $addressMetadata;

    /**
     * @var AvailabilityChecker
     */
    private $availabilityChecker;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @var string
     */
    private $addressType;

    /**
     * @param AddressMetadataInterface $addressMetadata
     * @param AvailabilityChecker $availabilityChecker
     * @param Config $config
     * @param BooleanUtils $booleanUtils
     * @param string $addressType
     */
    public function __construct(
        AddressMetadataInterface $addressMetadata,
        AvailabilityChecker $availabilityChecker,
        Config $config,
        BooleanUtils $booleanUtils,
        $addressType
    ) {
        $this->addressMetadata = $addressMetadata;
        $this->availabilityChecker = $availabilityChecker;
        $this->config = $config;
        $this->booleanUtils = $booleanUtils;
        $this->addressType = $addressType;
    }

    /**
     * Get metadata
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getMetadata()
    {
        $metadata = [];
        $attrMetadata = $this->addressMetadata->getAttributes('customer_register_address');
        $formConfig = $this->config->getAddressFormConfig($this->addressType);
        foreach ($attrMetadata as $meta) {
            if (!$this->availabilityChecker->isAvailableOnForm($meta)) {
                continue;
            }
            $attributeCode = $meta->getAttributeCode();
            if ($attributeCode == 'region_id') {
                continue;
            }
            $customMeta = isset($formConfig['attributes'][$attributeCode])
                ? $formConfig['attributes'][$attributeCode]
                : null;

            if ($this->isMultiline($meta)) {
                if ($customMeta && !array_key_exists(0, $customMeta)) {
                    $customMeta = [$customMeta];
                }
                $metaMultilineCount = $meta->getMultilineCount();
                for ($line = 0; $line < $metaMultilineCount; $line++) {
                    if ($customMeta && isset($customMeta[$line])) {
                        if ($this->booleanUtils->toBoolean($customMeta[$line]['visible'])) {
                            $metadata[] = [
                                'code' => $attributeCode . '_line_' . $line,
                                'label' => $customMeta[$line]['label'],
                                'required' => $this->booleanUtils->toBoolean($customMeta[$line]['required'])
                            ];
                        }
                    } else {
                        $metadata[] = [
                            'code' => $attributeCode . '_line_' . $line,
                            'label' => $meta->getStoreLabel() . ' Line ' . ($line > 0 ? $line : ''),
                            'required' => $line > 0 ? $meta->isRequired() : false
                        ];
                    }
                }
            } else {
                if ($customMeta) {
                    if (array_key_exists(0, $customMeta)) {
                        $customMeta = $customMeta[0];
                    }
                    if ($this->booleanUtils->toBoolean($customMeta['visible'])) {
                        $metadata[] = [
                            'code' => $attributeCode,
                            'label' => $customMeta['label'],
                            'required' => $this->booleanUtils->toBoolean($customMeta['required'])
                        ];
                    }
                } else {
                    if ($meta->isVisible()) {
                        $metadata[] = [
                            'code' => $attributeCode,
                            'label' => $meta->getStoreLabel(),
                            'required' => $meta->isRequired()
                        ];
                    }
                }
            }
        }

        return $metadata;
    }

    /**
     * Check if attribute should be handled as multiline one
     *
     * @param AttributeMetadataInterface $meta
     * @return bool
     */
    private function isMultiline($meta)
    {
        return $meta->getMultilineCount() > 1
            || $meta->getAttributeCode() == 'street';
    }
}
