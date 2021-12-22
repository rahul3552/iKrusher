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
namespace Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilter;

use Aheadworks\OneStepCheckout\Model\Report\Source\DateRange as DateRangeSource;
use Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilter\DateRange\Resolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class DateRange
 * @package Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilter
 */
class DateRange
{
    const REQUEST_DATE_RANGE_FIELD_NAME = 'date_range';
    const REQUEST_FROM_DATE_FIELD_NAME = 'date_range_from';
    const REQUEST_TO_DATE_FIELD_NAME = 'date_range_to';

    /**
     * Session param key
     */
    const SESSION_KEY = 'aw_osc_date_range';

    /**
     * Default date range value
     */
    const DEFAULT_RANGE_VALUE = DateRangeSource::THIS_MONTH;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var Resolver
     */
    private $dateRangeResolver;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param RequestInterface $request
     * @param SessionManagerInterface $session
     * @param Resolver $dateRangeResolver
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        RequestInterface $request,
        SessionManagerInterface $session,
        Resolver $dateRangeResolver,
        TimezoneInterface $localeDate
    ) {
        $this->request = $request;
        $this->session = $session;
        $this->dateRangeResolver = $dateRangeResolver;
        $this->localeDate = $localeDate;
    }

    /**
     * Get filter value
     *
     * @return array
     */
    public function getValue()
    {
        $value = [];
        $range = self::DEFAULT_RANGE_VALUE;
        $sessionData = $this->session->getData(self::SESSION_KEY);

        $requestRangeParamValue = $this->request->getParam(self::REQUEST_DATE_RANGE_FIELD_NAME);
        if ($requestRangeParamValue !== null) {
            $range = $requestRangeParamValue;
        } else {
            if ($sessionData !== null) {
                $range = $sessionData['range'];
            }
        }
        $value['range'] = $range;
        if ($range == DateRangeSource::CUSTOM) {
            $periodData = [];

            $requestFromParamValue = $this->request->getParam(self::REQUEST_FROM_DATE_FIELD_NAME);
            $timezone = $this->localeDate->getConfigTimezone(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
            if ($requestFromParamValue !== null) {
                $periodData['from'] = new \DateTime($requestFromParamValue, new \DateTimeZone($timezone));
            } elseif (isset($sessionData['from'])) {
                $periodData['from'] = $sessionData['from'];
            }
            $requestToParamValue = $this->request->getParam(self::REQUEST_TO_DATE_FIELD_NAME);
            if ($requestToParamValue !== null) {
                $periodData['to'] = new \DateTime($requestToParamValue, new \DateTimeZone($timezone));
            } elseif (isset($sessionData['to'])) {
                $periodData['to'] = $sessionData['to'];
            }

            $value = array_merge($value, $periodData);
        } else {
            $value = array_merge($value, $this->dateRangeResolver->resolve($value['range']));
        }

        $this->session->setData(self::SESSION_KEY, $value);

        return $value;
    }
}
