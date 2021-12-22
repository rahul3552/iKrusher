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
namespace Aheadworks\OneStepCheckout\Model\Product\Configuration;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class SerializerFactory
 * @package Aheadworks\OneStepCheckout\Model
 */
class SerializerFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->objectManager = $objectManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Create serializer instance
     *
     * @return SerializerInterface
     */
    public function create()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $serializerClassName = version_compare($magentoVersion, '2.2.0', '>=')
            ? \Magento\Framework\Serialize\Serializer\Json::class
            : \Magento\Framework\Serialize\Serializer\Serialize::class;

        return $this->objectManager->create($serializerClassName);
    }
}
