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
namespace Aheadworks\OneStepCheckout\Model\DeliveryDate;

use Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate\DisplayOption as DeliveryDateDisplayOption;
use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class PaymentDataExtensionProcessor
 * @package Aheadworks\OneStepCheckout\Model\DeliveryDate
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PaymentDataExtensionProcessor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var DateTime
     */
    private $dateFormat;

    /**
     * @var TimezoneInterface
     */
    private $timeZoneResolver;

    /**
     * @param Config $config
     * @param CartRepositoryInterface $quoteRepository
     * @param DateTime $dateFormat
     * @param TimezoneInterface $timeZoneResolver
     */
    public function __construct(
        Config $config,
        CartRepositoryInterface $quoteRepository,
        DateTime $dateFormat,
        TimezoneInterface $timeZoneResolver
    ) {
        $this->config = $config;
        $this->quoteRepository = $quoteRepository;
        $this->dateFormat = $dateFormat;
        $this->timeZoneResolver = $timeZoneResolver;
    }

    /**
     * Process delivery date extension attributes of payment data
     *
     * @param PaymentInterface $paymentData
     * @param int $cartId
     * @return void
     */
    public function process(PaymentInterface $paymentData, $cartId)
    {
        if ($this->config->getDeliveryDateDisplayOption() != DeliveryDateDisplayOption::NO) {
            $deliveryDate = $paymentData->getExtensionAttributes() === null
                ? false
                : $paymentData->getExtensionAttributes()->getDeliveryDate();
            $deliveryTimeSlot = $paymentData->getExtensionAttributes() === null
                ? false
                : $paymentData->getExtensionAttributes()->getDeliveryTimeSlot();

            if ($deliveryDate) {
                $quote = $this->quoteRepository->getActive($cartId);
                $quote->setAwDeliveryDate($this->getPreparedDateTime($deliveryDate));

                if ($deliveryTimeSlot) {
                    $fromTo = explode('-', $deliveryTimeSlot);
                    $quote->setAwDeliveryDateFrom($this->getPreparedDateTime($deliveryDate, $fromTo[0]))
                        ->setAwDeliveryDateTo($this->getPreparedDateTime($deliveryDate, $fromTo[1]));
                }
                $this->quoteRepository->save($quote);
            }
        }
    }

    /**
     * Get prepared date/time
     *
     * @param string $date
     * @param int|null $time
     * @return string
     */
    private function getPreparedDateTime($date, $time = null)
    {
        $timezone = $this->timeZoneResolver->getConfigTimezone();
        $date = new \DateTime($date, new \DateTimeZone($timezone));
        if ($time) {
            $date->add(new \DateInterval('PT' . $time . 'S'));
        }
        return $this->timeZoneResolver->convertConfigTimeToUtc($date);
    }
}
