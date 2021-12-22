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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Aheadworks\Ctq\Model\ResourceModel\Quote\Collection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassChangeStatus
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class MassChangeStatus extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function massAction(Collection $collection)
    {
        $status = $this->getRequest()->getParam('status');
        $updatedRecords = 0;
        foreach ($collection->getAllIds() as $quoteId) {
            try {
                $this->sellerQuoteManagement->changeStatus($quoteId, $status);
                $updatedRecords++;
            } catch (LocalizedException $e) {
            }
        }

        if ($updatedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updatedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records have been updated.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
