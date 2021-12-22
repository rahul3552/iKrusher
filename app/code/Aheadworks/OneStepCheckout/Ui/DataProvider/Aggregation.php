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
namespace Aheadworks\OneStepCheckout\Ui\DataProvider;

use Aheadworks\OneStepCheckout\Model\Report\Source\Aggregation as AggregationSource;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * Class Aggregation
 * @package Aheadworks\OneStepCheckout\Ui\DataProvider
 */
class Aggregation
{
    const REQUEST_FIELD_NAME = 'aggregated_by';

    /**
     * Session param key
     */
    const SESSION_KEY = 'aw_osc_aggregated_by';

    const DEFAULT_AGGREGATION = AggregationSource::WEEK;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @param RequestInterface $request
     * @param SessionManagerInterface $session
     */
    public function __construct(
        RequestInterface $request,
        SessionManagerInterface $session
    ) {
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Get current aggregation
     *
     * @return string
     */
    public function getAggregation()
    {
        $aggregationType = self::DEFAULT_AGGREGATION;

        $requestParamValue = $this->request->getParam(self::REQUEST_FIELD_NAME);
        if ($requestParamValue) {
            $aggregationType = $requestParamValue;
        } else {
            $sessionDataValue = $this->session->getData(self::SESSION_KEY);
            if ($sessionDataValue) {
                $aggregationType = $sessionDataValue;
            }
        }
        $this->session->setData(self::SESSION_KEY, $aggregationType);

        return $aggregationType;
    }
}
