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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\Customization;

use Aheadworks\OneStepCheckout\Model\Config as ModuleConfig;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\OneStepCheckout\Model\Address\Attribute\Code\Resolver as AddressAttributeCodeResolver;

/**
 * Class Modifier
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\Customization
 */
class Modifier
{
    /**
     * @var ModuleConfig
     */
    private $config;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var AddressAttributeCodeResolver
     */
    private $addressAttributeCodeResolver;

    /**
     * @param ModuleConfig $config
     * @param DataObjectHelper $dataObjectHelper
     * @param AddressAttributeCodeResolver $addressAttributeCodeResolver
     */
    public function __construct(
        ModuleConfig $config,
        DataObjectHelper $dataObjectHelper,
        AddressAttributeCodeResolver $addressAttributeCodeResolver
    ) {
        $this->config = $config;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->addressAttributeCodeResolver = $addressAttributeCodeResolver;
    }

    /**
     * Modify attribute metadata
     *
     * @param string $attributeCode
     * @param AttributeMetadataInterface $metadata
     * @param string $addressType
     * @return AttributeMetadataInterface
     */
    public function modify($attributeCode, $metadata, $addressType)
    {
        $formConfig = $this->config->getAddressFormConfig($addressType);
        $metadataUpdate = $this->getMetadataFromConfig($attributeCode, $formConfig);
        if (!empty($metadataUpdate)
            && $metadata->getFrontendInput() != 'multiline'
        ) {
            $this->dataObjectHelper->populateWithArray(
                $metadata,
                [
                    AttributeMetadataInterface::STORE_LABEL => $metadataUpdate['label'],
                    AttributeMetadataInterface::VISIBLE => $metadataUpdate['visible'],
                    AttributeMetadataInterface::REQUIRED => $metadataUpdate['required']
                ],
                AttributeMetadataInterface::class
            );
        }
        return $metadata;
    }

    /**
     * Retrieve config metadata for specific attribute
     *
     * @param string $attributeCode
     * @param array $formConfig
     * @return array
     */
    private function getMetadataFromConfig($attributeCode, $formConfig)
    {
        $attributeMetadata = [];
        $attributesFormConfig = isset($formConfig['attributes']) ? $formConfig['attributes'] : [];
        $duplicatedAttributeCode = $this->addressAttributeCodeResolver->getDuplicatedAttributeCode($attributeCode);

        if (isset($attributesFormConfig[$attributeCode])) {
            $attributeMetadata = $attributesFormConfig[$attributeCode];
        } elseif (isset($attributesFormConfig[$duplicatedAttributeCode])) {
            $attributeMetadata = $attributesFormConfig[$duplicatedAttributeCode];
        }

        return $attributeMetadata;
    }
}
