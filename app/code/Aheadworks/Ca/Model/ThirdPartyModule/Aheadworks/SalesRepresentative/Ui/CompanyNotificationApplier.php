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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\SalesRepresentative\Ui;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Backend\Block\Template;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\SalesRepresentative\ViewModel\CustomerCompanyMessage;

/**
 * Class CompanyNotificationApplier
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\SalesRepresentative\Ui
 */
class CompanyNotificationApplier
{
    /**
     * Component name for message rendering
     */
    const MESSAGE_COMPONENT_NAME = 'message-container';

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var CustomerCompanyMessage
     */
    private $customerCompanyMessageViewModel;

    /**
     * @var array
     */
    private $componentListToHide = [
        'aw_salesrep_id' => true
    ];

    /**
     * @param LayoutInterface $layout
     * @param CustomerCompanyMessage $customerCompanyMessageViewModel
     * @param array $componentListToHide
     */
    public function __construct(
        LayoutInterface $layout,
        CustomerCompanyMessage $customerCompanyMessageViewModel,
        array $componentListToHide = []
    ) {
        $this->layout = $layout;
        $this->customerCompanyMessageViewModel = $customerCompanyMessageViewModel;
        $this->componentListToHide = array_merge($this->componentListToHide, $componentListToHide);
    }

    /**
     * Hide sales representative components
     *
     * @param UiComponentInterface $component
     */
    public function hideSalesRepComponents(UiComponentInterface $component)
    {
        $childComponents = $component->getChildComponents();
        foreach ($childComponents as $child) {
            if (isset($this->componentListToHide[$child->getName()])) {
                $config = $child->getData('config');
                $config['componentDisabled'] = $this->componentListToHide[$child->getName()];
                $child->setData('config', $config);
            }
        }
    }

    /**
     * Apply notification
     *
     * @param UiComponentInterface $component
     * @param int $companyId
     */
    public function applyNotification($component, $companyId)
    {
        $childComponents = $component->getChildComponents();
        if (isset($childComponents[self::MESSAGE_COMPONENT_NAME])) {
            $messageContainer = $childComponents[self::MESSAGE_COMPONENT_NAME];
            $config = $messageContainer->getData('config');
            $config['componentDisabled'] = false;
            $config['message'] = $this->getHtmlMessage($companyId);
            $messageContainer->setData('config', $config);
        }
    }

    /**
     * Get html message
     *
     * @param int $companyId
     * @return string
     */
    private function getHtmlMessage($companyId)
    {
        $messageBlock = $this->layout->createBlock(
            Template::class,
            'aw_sales_rep_customer_company_message',
            [
                'data' => [
                    'template' => 'Aheadworks_Ca::'
                        . 'third_party_module/sales_representative/customer_company_message.phtml',
                    'company_id' => $companyId,
                    'view_model' => $this->customerCompanyMessageViewModel
                ]
            ]
        );

        return $messageBlock->toHtml();
    }
}
