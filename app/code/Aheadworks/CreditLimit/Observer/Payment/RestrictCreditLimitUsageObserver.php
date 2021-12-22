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
namespace Aheadworks\CreditLimit\Observer\Payment;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Quote\Model\Quote;
use Magento\Payment\Model\Method\AbstractMethod;
use Aheadworks\CreditLimit\Model\Checkout\ConfigProvider;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Model\Payment\AvailabilityChecker;

/**
 * Class RestrictCreditLimitUsageObserver
 *
 * @package Aheadworks\CreditLimit\Observer\Payment
 */
class RestrictCreditLimitUsageObserver implements ObserverInterface
{
    /**
     * @var AvailabilityChecker
     */
    private $availabilityChecker;

    /**
     * @param AvailabilityChecker $availabilityChecker
     */
    public function __construct(
        AvailabilityChecker $availabilityChecker
    ) {
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * Restrict credit limit payment usage
     *
     * @param EventObserver $observer
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        $event = $observer->getEvent();
        /** @var AbstractMethod $methodInstance */
        $methodInstance = $event->getMethodInstance();
        /** @var Quote $quote */
        $quote = $event->getQuote();

        if ($methodInstance->getCode() == ConfigProvider::METHOD_CODE
            && !$this->isCreditLimitAvailable($quote)
        ) {
            /** @var DataObject $result */
            $result = $observer->getEvent()->getResult();
            $result->setData('is_available', false);
        }
    }

    /**
     * Check if credit limit is available
     *
     * @param Quote|null $quote
     * @return bool
     */
    private function isCreditLimitAvailable($quote)
    {
        return $quote->getIsSuperMode()
            ? $this->availabilityChecker->isAvailableInAdmin($quote)
            : $this->availabilityChecker->isAvailableOnFrontend($quote);
    }
}
