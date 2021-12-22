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
namespace Aheadworks\OneStepCheckout\Model\Config\Backend\TrustSeals;

use Aheadworks\OneStepCheckout\Model\Config\Backend\TrustSeals\Badges\Validator;
use Aheadworks\OneStepCheckout\Model\Config\Backend\ConfigValue;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Badges
 * @package Aheadworks\OneStepCheckout\Model\Config\Backend\TrustSeals
 */
class Badges extends ConfigValue
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param Validator $validator
     * @param SerializerInterface $serializer
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Validator $validator,
        SerializerInterface $serializer,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $serializer,
            $resource,
            $resourceCollection,
            $data
        );
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $value = $this->resolveSerializedValue();
        if (is_array($value)) {
            unset($value['__empty']);
            $value = array_values($value);
        }
        $this->setValue($this->serializer->serialize($value));
        return $this;
    }

    /**
     * Process data after load
     *
     * @return $this
     */
    public function afterLoad()
    {
        if (empty($this->getValue())) {
            return $this;
        }
        $value = $this->serializer->unserialize($this->getValue());
        if (is_array($value)) {
            $value = $this->prepareValue($value);
            $this->setValue($value);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }

    /**
     * Prepare value before saving
     * Escape quotes
     *
     * @param array $value
     * @return array
     */
    private function prepareValue($value)
    {
        foreach ($value as &$badgeData) {
            if (is_array($badgeData) && array_key_exists('script', $badgeData)) {
                $badgeData['script'] = str_ireplace("'", "&#039;", $badgeData['script']);
            }
        }

        return $value;
    }
}
