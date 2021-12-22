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

use Magento\Framework\ObjectManagerInterface;

/**
 * Class MetaProviderFactory
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior\AddressAttributes
 */
class MetaProviderFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create meta provider instance
     *
     * @param string $className
     * @return MetaProvider
     */
    public function create($className)
    {
        return $this->objectManager->create($className);
    }
}
