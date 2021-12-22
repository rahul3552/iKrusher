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
namespace Aheadworks\OneStepCheckout\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class ConfigValue
 * @package Aheadworks\OneStepCheckout\Model\Config\Backend
 */
class ConfigValue extends Value
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param SerializerInterface $serializer
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        SerializerInterface $serializer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
        $this->serializer = $serializer;
    }

    /**
     * Get serialized value
     *
     * @return mixed
     */
    public function resolveSerializedValue()
    {
        $currentValue = $this->getValue();
        $resolvedValue = $currentValue;
        if (is_string($currentValue)) {
            try {
                $unserializedValue = $this->serializer->unserialize($currentValue);
            } catch (\Exception $exception) {
                $unserializedValue = false;
            }
            if ($unserializedValue !== false) {
                $resolvedValue = $unserializedValue;
            }
        }

        return $resolvedValue;
    }
}
