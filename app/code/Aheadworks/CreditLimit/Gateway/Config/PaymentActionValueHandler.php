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
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order as SalesOrder;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class PaymentActionValueHandler
 *
 * @package Aheadworks\CreditLimit\Gateway\Config
 */
class PaymentActionValueHandler implements ValueHandlerInterface
{
    /**
     * @var ConfigInterface
     */
    private $configInterface;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param ConfigInterface $configInterface
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ConfigInterface $configInterface,
        SubjectReader $subjectReader
    ) {
        $this->configInterface = $configInterface;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $subject, $storeId = null)
    {
        $orderStatus = $this->configInterface->getValue('order_status', $storeId);
        if ($orderStatus != SalesOrder::STATE_PROCESSING) {
            $result = AbstractMethod::ACTION_ORDER;
        } else {
            $result = $this->configInterface->getValue($this->subjectReader->readField($subject), $storeId);
        }

        return $result;
    }
}
