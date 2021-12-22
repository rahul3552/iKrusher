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
namespace Aheadworks\CreditLimit\Block\Customer\BalanceHistory;

use Magento\Ui\Block\Wrapper;
use Magento\Framework\View\Element\Template\Context;
use Magento\Ui\Model\UiComponentGenerator;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Listing
 *
 * @package Aheadworks\CreditLimit\Block\Customer\BalanceHistory
 */
class Listing extends Wrapper
{
    /**
     * @param Context $context
     * @param UiComponentGenerator $uiComponentGenerator
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        UiComponentGenerator $uiComponentGenerator,
        CustomerSession $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $uiComponentGenerator, $data);
        $this->addData($this->getComponentConfigData($customerSession));
    }

    /**
     * Retrieve config data for component data provider
     *
     * @param CustomerSession $customerSession
     * @return array
     *
     * @see Wrapper::injectDataInDataSource()
     */
    private function getComponentConfigData($customerSession)
    {
        $params = [
            'customer_id' => $customerSession->getCustomerId()
        ];

        return [
            'params' => $this->getComponentParams($params)
        ];
    }

    /**
     * This method is used to apply additional params using plugin system
     *
     * @param array $params
     * @return array
     */
    public function getComponentParams($params)
    {
        return $params;
    }
}
