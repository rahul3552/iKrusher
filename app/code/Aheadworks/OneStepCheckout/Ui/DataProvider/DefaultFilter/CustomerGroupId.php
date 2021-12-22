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

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * Class CustomerGroupId
 * @package Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilter
 */
class CustomerGroupId
{
    const REQUEST_FIELD_NAME = 'customer_group_id';

    /**
     * Session param key
     */
    const SESSION_KEY = 'aw_osc_customer_group_id';

    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @param GroupManagementInterface $groupManagement
     * @param RequestInterface $request
     * @param SessionManagerInterface $session
     */
    public function __construct(
        GroupManagementInterface $groupManagement,
        RequestInterface $request,
        SessionManagerInterface $session
    ) {
        $this->groupManagement = $groupManagement;
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * todo: consider getValue() method name
     * Get current customer group Id
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        $customerGroupId = null;
        $requestParamValue = $this->request->getParam(self::REQUEST_FIELD_NAME);
        if ($requestParamValue !== null) {
            $customerGroupId = $requestParamValue;
        } else {
            $sessionDataValue = $this->session->getData(self::SESSION_KEY);
            if ($sessionDataValue !== null) {
                $customerGroupId = $sessionDataValue;
            }
        }
        if ($customerGroupId === null) {
            $allGroup = $this->groupManagement->getAllCustomersGroup();
            $customerGroupId = $allGroup->getId();
        }
        $this->session->setData(self::SESSION_KEY, $customerGroupId);

        return $customerGroupId;
    }
}
