<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Controller\Adminhtml\Form;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\CustomForm\Controller\Adminhtml\Form as AbstractForm;

/**
 * Class Delete
 * @package Mageplaza\CustomForm\Controller\Adminhtml\Form
 */
class Delete extends AbstractForm
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $customForm = $this->formFactory->create();
                $this->formResource->load($customForm, $id);
                if ($customForm->getId()) {
                    $this->formResource->delete($customForm);
                    $this->messageManager->addSuccessMessage(__('The Form has been deleted.'));
                } else {
                    $this->messageManager->addErrorMessage(__('Form to delete was not found.'));
                }
            } catch (Exception $e) {
                /** display error message */
                $this->messageManager->addErrorMessage($e->getMessage());
                /** go back to edit form */
                $resultRedirect->setPath('*/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
        } else {
            /** display error message */
            $this->messageManager->addErrorMessage(__('Form to delete was not found.'));
        }

        /** goto grid */
        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }
}
