<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentJournal
 */

namespace I95DevConnect\PaymentJournal\Block\Adminhtml\Order\View;

/**
 * Block for displaying cash receipt information in order view page
 * @api
 */
class Info extends \Magento\Backend\Block\Template
{

    /**
     * @var \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory
     */
    public $paymentJournalFactory;

    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * Info constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory $paymentJournalFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory $paymentJournalFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->paymentJournalFactory = $paymentJournalFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get current order
     * @return string|null
     * @author Hrusikesh Manna
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }
    
    /**
     * Get Cash Receipt Id
     * @return array|null
     * @author Hrusikesh Manna
     */
    public function getCashReceipt()
    {
        $order = $this->getOrder();
        $customCollection = $this->paymentJournalFactory->create()->getCollection();
        $customCollection->addFieldToSelect('receipt_id')
                ->addFieldToFilter('source_order_id', $order->getId());
        $customCollection->getSelect()->limit(1);
        return $customCollection->getData();
    }
}
