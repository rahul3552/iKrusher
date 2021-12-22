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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin;

use Aheadworks\Ca\Model\Source\Customer\CompanyUser\Status as CompanyUserStatusSource;
use Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\CustomerSelection\Grid;

/**
 * Class CustomerSelectionGridPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin
 */
class CustomerSelectionGridPlugin
{
    /**
     * @var CompanyUserStatusSource
     */
    private $companyUserStatusSource;

    /**
     * @param CompanyUserStatusSource $companyUserStatusSource
     */
    public function __construct(CompanyUserStatusSource $companyUserStatusSource)
    {
        $this->companyUserStatusSource = $companyUserStatusSource;
    }

    /**
     * Add column to Customer Selection Grid
     *
     * @param Grid $subject
     * @return void
     */
    public function beforeToHtml($subject)
    {
        $subject->addColumnAfter(
            'aw_ca_company',
            [
                'header' => __('Company'),
                'index' => 'aw_ca_company',
                'filter' => false,
                'sortable' => false,
            ],
            'website_name'
        );
        $subject->addColumnAfter(
            'aw_ca_is_activated',
            [
                'header' => __('Status in Company'),
                'index' => 'aw_ca_is_activated',
                'type' => 'options',
                'sortable' => false,
                'filter' => false,
                'options' => $this->companyUserStatusSource->getOptionArray()
            ],
            'aw_ca_company'
        );
    }
}
