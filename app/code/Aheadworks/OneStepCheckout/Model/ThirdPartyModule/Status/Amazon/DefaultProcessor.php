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
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon;

use Magento\Framework\ObjectManagerInterface;
use Amazon\Core\Helper\Data;

/**
 * Class DefaultProcessor
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon
 */
class DefaultProcessor implements StatusInterface
{
    /**
     * AbstractHelper Amazon Core Helper Class
     */
    const HELPER_CLASS_NAME = Data::class;

    /**
     * @var Data
     */
    private $amazonHelper;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->amazonHelper = $objectManager->create(self::HELPER_CLASS_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function isPwaEnabled()
    {
        return $this->amazonHelper->isPwaEnabled();
    }
}
