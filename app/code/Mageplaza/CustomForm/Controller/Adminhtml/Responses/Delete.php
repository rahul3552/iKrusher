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

namespace Mageplaza\CustomForm\Controller\Adminhtml\Responses;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Registry;
use Mageplaza\CustomForm\Controller\Adminhtml\Form as AbstractForm;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\Form;
use Mageplaza\CustomForm\Model\FormFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Form as FormResource;
use Mageplaza\CustomForm\Model\ResourceModel\Responses as ResponsesResource;
use Mageplaza\CustomForm\Model\ResponsesFactory;

/**
 * Class Delete
 * @package Mageplaza\CustomForm\Controller\Adminhtml\Responses
 */
class Delete extends AbstractForm
{
    /**
     * @var ResponsesFactory
     */
    private $responsesFactory;

    /**
     * @var ResponsesResource
     */
    private $responsesResource;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FormFactory $formFactory
     * @param FormResource $formResource
     * @param ResponsesFactory $responsesFactory
     * @param ResponsesResource $responsesResource
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FormFactory $formFactory,
        FormResource $formResource,
        ResponsesFactory $responsesFactory,
        ResponsesResource $responsesResource,
        Data $helperData
    ) {
        $this->responsesFactory  = $responsesFactory;
        $this->responsesResource = $responsesResource;

        parent::__construct($context, $coreRegistry, $formFactory, $formResource, $helperData);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            $response = $this->responsesFactory->create();
            $this->responsesResource->load($response, $id);
            $formId     = $response->getFormId();
            $customForm = $this->formFactory->create();
            $this->formResource->load($customForm, $formId);
            try {
                $this->responsesResource->delete($response);
                if ($customForm->getId()) {
                    $this->updateResponsesSummary($customForm);
                }

                $this->messageManager->addSuccessMessage(__('The Response has been deleted.'));
            } catch (Exception $e) {
                /** display error message */
                $this->messageManager->addErrorMessage($e->getMessage());
                /** go back to edit form */
                $resultRedirect->setPath('*/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
        } else {
            /** display error message */
            $this->messageManager->addErrorMessage(__('Response to delete was not found.'));
        }

        /** goto grid */
        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }

    /**
     * @param Form $customForm
     *
     * @throws AlreadyExistsException
     */
    protected function updateResponsesSummary($customForm)
    {
        $responsesSummary = $customForm->getResponsesSummary()
            ? Data::jsonDecode($customForm->getResponsesSummary()) : [];
        if (!empty($responsesSummary)) {
            foreach ($responsesSummary as &$page) {
                if (!is_array($page)) {
                    continue;
                }
                foreach ($page as &$fieldGroup) {
                    if (!is_array($fieldGroup)) {
                        continue;
                    }
                    foreach ($fieldGroup as &$field) {
                        if (isset($field[$customForm->getId()])) {
                            unset($field[$customForm->getId()]);
                        }
                    }
                    unset($field);
                }
                unset($fieldGroup);
            }
            unset($page);
        }
        $customForm->setResponsesSummary(Data::jsonEncode($responsesSummary));
        $this->formResource->save($customForm);
    }
}
