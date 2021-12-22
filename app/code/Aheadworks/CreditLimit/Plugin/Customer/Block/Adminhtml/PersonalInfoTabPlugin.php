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
namespace Aheadworks\CreditLimit\Plugin\Customer\Block\Adminhtml;

use Aheadworks\CreditLimit\Block\Adminhtml\Customer\TabActivator;
use Aheadworks\CreditLimit\Block\Adminhtml\Customer\TabActivatorFactory;
use Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo;

/**
 * Class PersonalInfoTabPlugin
 *
 * @package Aheadworks\CreditLimit\Plugin\Customer\Block\Adminhtml
 */
class PersonalInfoTabPlugin
{
    /**
     * Request param to trigger tab auto activation
     */
    const PARAM_TO_TRIGGER = 'tab';

    /**
     * Request param value
     */
    const PARAM_VALUE = 'aw_cl';

    /**
     * Tab ID to activate
     */
    const TAB_ID = 'tab_aw_credit_limit_data';

    /**
     * @var TabActivatorFactory
     */
    private $tabActivatorFactory;

    /**
     * @param TabActivatorFactory $tabActivatorFactory
     */
    public function __construct(
        TabActivatorFactory $tabActivatorFactory
    ) {
        $this->tabActivatorFactory = $tabActivatorFactory;
    }

    /**
     * Render tab activator to jump to credit limit tab
     *
     * @param PersonalInfo $subject
     * @param string $resultHtml
     * @return string
     */
    public function afterToHtml($subject, $resultHtml)
    {
        /** @var TabActivator $tabActivator */
        $tabActivator = $this->tabActivatorFactory->create(
            [
                'data' => [
                    'param_to_trigger' => self::PARAM_TO_TRIGGER,
                    'param_value' => self::PARAM_VALUE,
                    'tab_id' => self::TAB_ID
                ]
            ]
        );
        $resultHtml .= $tabActivator->toHtml();

        return $resultHtml;
    }
}
