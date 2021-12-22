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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * Class StoreGroupId
 * @package Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilter
 */
class StoreGroupId
{
    const REQUEST_FIELD_NAME = 'store_group_id';

    /**
     * Session param key
     */
    const SESSION_KEY = 'aw_osc_store_group_id';

    /**
     * Default filter value
     */
    const DEFAULT_VALUE = 0;

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
     * Get filter value
     *
     * @return int
     */
    public function getValue()
    {
        $value = self::DEFAULT_VALUE;

        $requestParamValue = $this->request->getParam(self::REQUEST_FIELD_NAME);
        if ($requestParamValue !== null) {
            $value = $requestParamValue;
        } else {
            $sessionDataValue = $this->session->getData(self::SESSION_KEY);
            if ($sessionDataValue !== null) {
                $value = $sessionDataValue;
            }
        }
        $this->session->setData(self::SESSION_KEY, $value);

        return $value;
    }
}
