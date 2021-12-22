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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Gateway\Config;

use Magento\Payment\Gateway\Config\ValueHandlerInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Sales\Model\Order;

/**
 * Class CanCaptureValueHandler
 *
 * @package Aheadworks\CreditLimit\Gateway\Config
 */
class CanCaptureValueHandler implements ValueHandlerInterface
{
    /**
     * @var ConfigInterface
     */
    private $configInterface;

    /**
     * @param ConfigInterface $configInterface
     */
    public function __construct(
        ConfigInterface $configInterface
    ) {
        $this->configInterface = $configInterface;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $subject, $storeId = null)
    {
        return $this->configInterface->getValue('order_status', $storeId) == Order::STATE_PROCESSING;
    }
}
