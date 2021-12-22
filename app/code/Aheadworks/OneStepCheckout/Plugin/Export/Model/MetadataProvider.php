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
namespace Aheadworks\OneStepCheckout\Plugin\Export\Model;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Ui\Model\Export\MetadataProvider as ExportMetadataProvider;

/**
 * Class MetadataProvider
 * @package Aheadworks\OneStepCheckout\Plugin\Export\Model
 */
class MetadataProvider
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param TimezoneInterface $timezone
     */
    public function __construct(TimezoneInterface $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Convert document aw_delivery_date(UTC) fields to default scope specified
     *
     * @param ExportMetadataProvider $subject
     * @param \Closure $proceed
     * @param DocumentInterface $document
     * @param string $componentName
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundConvertDate(ExportMetadataProvider $subject, \Closure $proceed, $document, $componentName)
    {
        if ($componentName == 'sales_order_grid') {
            $deliveryDate = $document->getData('aw_delivery_date');
            $deliveryDateFrom = $document->getData('aw_delivery_date_from');
            $deliveryDateTo = $document->getData('aw_delivery_date_to');

            if ($deliveryDate) {
                $deliveryDate = $this->timezone->formatDateTime(
                    $deliveryDate,
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::NONE
                );
                $document->setData('aw_delivery_date', $deliveryDate);
            }
            if ($deliveryDateFrom && $deliveryDateTo) {
                $fromTimeFormatted = $this->timezone->formatDateTime(
                    $deliveryDateFrom,
                    \IntlDateFormatter::NONE,
                    \IntlDateFormatter::SHORT
                );
                $toTimeFormatted = $this->timezone->formatDateTime(
                    $deliveryDateTo,
                    \IntlDateFormatter::NONE,
                    \IntlDateFormatter::SHORT
                );
                $document->setData('aw_delivery_time', $fromTimeFormatted . ' - ' . $toTimeFormatted);
            }
        }
        $proceed($document, $componentName);
    }
}
