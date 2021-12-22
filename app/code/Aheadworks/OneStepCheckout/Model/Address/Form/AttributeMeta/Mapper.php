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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Api\Data\OptionInterface;

/**
 * Class Mapper
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta
 */
class Mapper
{
    /**
     * @var array
     */
    private $formElementMap = [
        'text' => 'input',
        'hidden' => 'input',
        'boolean' => 'checkbox',
    ];

    /**
     * @var array
     */
    private $propertiesMap = [
        'dataType' => 'getFrontendInput',
        'visible' => 'isVisible',
        'required' => 'isRequired',
        'label' => 'getStoreLabel',
        'sortOrder' => 'getSortOrder',
        'notice' => 'getNote',
        'size' => 'getMultilineCount',
        'system' => 'isSystem'
    ];

    /**
     * @var array
     */
    private $defaultValidationRules = [
        'input_validation' => [
            'email' => ['validate-email' => true],
            'date' => ['validate-date' => true],
        ],
    ];

    /**
     * Map address attribute metadata
     *
     * @param AttributeMetadataInterface $metadata
     * @return array
     */
    public function map($metadata)
    {
        $result = [];
        foreach ($this->propertiesMap as $sourceFieldName => $methodName) {
            $result[$sourceFieldName] = $metadata->$methodName();
        }
        if (isset($result['dataType'])) {
            $dataType = $result['dataType'];
            $result['formElement'] = isset($this->formElementMap[$dataType])
                ? $this->formElementMap[$dataType]
                : $dataType;
        }
        if (count($metadata->getOptions())) {
            $result['options'] = $this->prepareOptions($metadata->getOptions());
        }
        $result['validation'] = $this->getValidationRules($metadata);
        return $result;
    }

    /**
     * Get validation rules data
     *
     * @param AttributeMetadataInterface $metadata
     * @return array
     */
    private function getValidationRules($metadata)
    {
        $rules = [];
        if ($metadata->isRequired()) {
            $rules['required-entry'] = true;
        }
        foreach ($metadata->getValidationRules() as $rule) {
            $name = $rule->getName();
            $value = $rule->getValue();
            if (isset($this->defaultValidationRules[$name][$value])) {
                foreach ($this->defaultValidationRules[$name][$value] as $key => $value) {
                    $rules[$key] = $value;
                }
            } else {
                $rules[$name] = $value;
            }
        }
        return $rules;
    }

    /**
     * Prepare options data
     *
     * @param OptionInterface[] $options
     * @return array
     */
    private function prepareOptions(array $options)
    {
        $optionsData = [];
        foreach ($options as $option) {
            $optionsData[] = [
                'value' => $option->getValue(),
                'label' => $option->getLabel()
            ];
        }
        return $optionsData;
    }
}
