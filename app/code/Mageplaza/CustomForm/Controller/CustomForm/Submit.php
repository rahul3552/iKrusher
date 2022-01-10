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

namespace Mageplaza\CustomForm\Controller\CustomForm;

use Exception;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\ResourceModel\Responses as ResponsesResource;
use Mageplaza\CustomForm\Model\ResponsesFactory as ResponsesModelFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Submit
 * @package Mageplaza\CustomForm\Controller\CustomForm
 */
class Submit extends Action
{
    /**
     * @var Request
     */
    protected $httpRequest;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ResponsesModelFactory
     */
    protected $responsesFactory;

    /**
     * @var ResponsesResource
     */
    protected $responsesResource;

    /**
     * Submit constructor.
     *
     * @param Context $context
     * @param Request $httpRequest
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param Data $helperData
     * @param ResponsesModelFactory $responsesFactory
     * @param ResponsesResource $responsesResource
     */
    public function __construct(
        Context $context,
        Request $httpRequest,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        Data $helperData,
        ResponsesModelFactory $responsesFactory,
        ResponsesResource $responsesResource
    ) {
        $this->httpRequest = $httpRequest;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->helperData = $helperData;
        $this->responsesFactory = $responsesFactory;
        $this->responsesResource = $responsesResource;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $response = $this->responsesFactory->create();
        /** @var CustomerSession $customerSession */
        $customerSession = $this->helperData->createObject(CustomerSession::class);
        $success = true;
        if ($this->getRequest()->getParam('formId')) {
            try {
                $response->addData([
                    'form_id' => $this->getRequest()->getParam('formId'),
                    'customer_id' => $customerSession->getCustomerId() ?: 0,
                    'store_ids' => $this->storeManager->getStore()->getId(),
                    'store_name' => $this->storeManager->getStore()->getName(),
                    'ip_address' => $this->httpRequest->getClientIp(),
                    'form_data' => Data::jsonEncode($this->getRequest()->getParam('pages')),
                ]);
                $this->responsesResource->save($response);
                $this->messageManager->addSuccessMessage(__('Your form have been submitted successfully '));
            } catch (Exception $e) {
                $success = false;
                $this->logger->critical($e->getMessage());
                $this->messageManager->addErrorMessage(__('Something went wrong while submit form %1'));
            }
        }
        if ($this->getRequest()->isAjax()) {
            return $this->getResponse()->representJson(Data::jsonEncode(['success' => $success]));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $afterSubmitUrl = $this->getRequest()->getParam('afterSubmitUrl');

        return $resultRedirect->setUrl($afterSubmitUrl);
    }
}
