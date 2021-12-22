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
namespace Aheadworks\OneStepCheckout\Model\Order\Email\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Variable
 * @package Aheadworks\OneStepCheckout\Model\Order\Email\Source
 */
class Variable implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $variables;

    /**
     * @param array $variables
     */
    public function __construct($variables = [])
    {
        $this->variables[] = ['value' => 'order.getAwDeliveryDateFormatted()', 'label' => __('Order Delivery Date')];
        $this->variables[] = ['value' => 'order.getAwDeliveryTimeFormatted()', 'label' => __('Order Delivery Time')];
    }

    /**
     * Retrieve option array additional order variables
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->variables as $variable) {
            $optionArray[] = [
                'value' => '{{var ' . $variable['value'] . '}}',
                'label' => $variable['label'],
            ];
        }
        return $optionArray;
    }

    /**
     * Check if template is related to sales order
     *
     * @param string $templateId
     * @return bool
     */
    public function isSalesOrderTemplate($templateId)
    {
        $salesOrderTemplates = [
            'sales_email_order_template',
            'sales_email_order_guest_template',
            'sales_email_order_comment_template',
            'sales_email_order_comment_guest_template',
            'sales_email_invoice_template',
            'sales_email_invoice_guest_template',
            'sales_email_invoice_comment_template',
            'sales_email_invoice_comment_guest_template',
            'sales_email_creditmemo_template',
            'sales_email_creditmemo_guest_template',
            'sales_email_creditmemo_comment_template',
            'sales_email_creditmemo_comment_guest_template',
            'sales_email_shipment_template',
            'sales_email_shipment_guest_template',
            'sales_email_shipment_comment_template',
            'sales_email_shipment_comment_guest_template',
        ];

        return in_array($templateId, $salesOrderTemplates);
    }
}
