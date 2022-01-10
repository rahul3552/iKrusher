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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page as PageResult;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Responses as ResponsesResource;
use Mageplaza\CustomForm\Model\Responses;
use Mageplaza\CustomForm\Model\ResponsesFactory;

/**
 * Class Edit
 * @package Mageplaza\CustomForm\Controller\Adminhtml\Responses
 */
class Edit extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mageplaza_CustomForm::responses';

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ResponsesFactory
     */
    protected $responsesFactory;

    /**
     * @var ResponsesResource
     */
    protected $responsesResource;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ResponsesFactory $responsesFactory
     * @param ResponsesResource $responsesResource
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ResponsesFactory $responsesFactory,
        ResponsesResource $responsesResource
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->responsesFactory = $responsesFactory;
        $this->responsesResource = $responsesResource;

        parent::__construct($context);
    }

    /**
     * @return Page|Redirect|PageResult
     */
    public function execute()
    {
        /** @var Responses $response */
        $response = $this->initResponse(true);
        if (!$response) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('mageplaza_custom_form_form_data', true);
        if (!empty($data)) {
            $response->setData($data);
        }

        /** @var Page|PageResult $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_CustomForm::responses');
        $resultPage->getConfig()->getTitle()->set(__('Response'));

        $title = __("View Customer's Response");
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }

    /**
     * @param bool $register
     *
     * @return bool|Responses
     */
    protected function initResponse($register = false)
    {
        $responseId = (int)$this->getRequest()->getParam('id');

        /** @var Responses $response */
        $response = $this->responsesFactory->create();
        if ($responseId) {
            $this->responsesResource->load($response, $responseId);
            if (!$response->getId()) {
                $this->messageManager->addErrorMessage(__('This response no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->coreRegistry->register('mageplaza_custom_form_response', $response);
        }

        return $response;
    }
}
