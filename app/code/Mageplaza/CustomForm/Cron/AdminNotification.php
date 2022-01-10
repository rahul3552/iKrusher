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

namespace Mageplaza\CustomForm\Cron;

use Exception;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\CustomForm\Block\Adminhtml\Responses\Edit\ResponseForm;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\Form;
use Mageplaza\CustomForm\Model\ResourceModel\Form\Collection as FormCollection;
use Mageplaza\CustomForm\Model\ResourceModel\Form\CollectionFactory as FormCollectionFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Responses as ResponsesResource;
use Mageplaza\CustomForm\Model\ResourceModel\Responses\Collection as ResponseCollection;
use Mageplaza\CustomForm\Model\ResourceModel\Responses\CollectionFactory as ResponsesCollectionFactory;
use Mageplaza\CustomForm\Model\Responses;
use Psr\Log\LoggerInterface;

/**
 * Class AdminNotification
 * @package Mageplaza\CustomForm\Cron
 */
class AdminNotification
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var FormCollectionFactory
     */

    protected $formCollectionFactory;

    /**
     * @var ResponsesCollectionFactory
     */
    protected $responsesCollectionFactory;

    /**
     * @var ResponsesResource
     */
    protected $responsesResource;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * AdminNotification constructor.
     *
     * @param LoggerInterface $logger
     * @param FormCollectionFactory $formCollectionFactory
     * @param ResponsesCollectionFactory $responsesCollectionFactory
     * @param ResponsesResource $responsesResource
     * @param Data $helperData
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        LoggerInterface $logger,
        FormCollectionFactory $formCollectionFactory,
        ResponsesCollectionFactory $responsesCollectionFactory,
        ResponsesResource $responsesResource,
        Data $helperData,
        PageFactory $resultPageFactory
    ) {
        $this->logger                     = $logger;
        $this->formCollectionFactory      = $formCollectionFactory;
        $this->responsesCollectionFactory = $responsesCollectionFactory;
        $this->responsesResource          = $responsesResource;
        $this->helperData                 = $helperData;
        $this->resultPageFactory          = $resultPageFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$this->helperData->isEnabled()) {
            return;
        }
        /** @var FormCollection $formCollection */
        $formCollection = $this->formCollectionFactory->create()
            ->addFieldToFilter('status', 1);
        $formCollection = $this->addFrequencyFilter($formCollection);
        /** @var Form $form */
        foreach ($formCollection as $form) {
            $adminNofEnabled = $form->getAdminNofEnabled();
            $isAttachedFiles = $form->getAdminNofAttachedFiles();
            $ccEmails        = $form->getAdminNofCcToEmail();
            if ($ccEmails === 'mp-use-config') {
                $ccEmails = $this->helperData->getAdminCCEmail();
            }
            $bccEmails = $form->getAdminNofBccToEmail();
            if ($bccEmails === 'mp-use-config') {
                $bccEmails = $this->helperData->getAdminBCCEmail();
            }
            if ($adminNofEnabled === Data::USE_CONFIG_VAL) {
                $adminNofEnabled = $this->helperData->getAdminNofEnabled();
            }
            if ($isAttachedFiles === Data::USE_CONFIG_VAL) {
                $isAttachedFiles = $this->helperData->getAdminAttachedFile();
            }
            $sender = $form->getAdminNofSender();
            if ($sender === 'mp-use-config') {
                $sender = $this->helperData->getAdminNofSender();
            }
            $sendTo = $form->getAdminNofSendTo();
            if ($sendTo === 'mp-use-config') {
                $sendTo = $this->helperData->getAdminNofSendTo();
            }
            $emailTemplate = $form->getAdminNofTemplate();
            if ($emailTemplate === 'mp-use-config') {
                $emailTemplate = $this->helperData->getAdminNofEmailTemplate();
            }
            $sendTo = trim($sendTo);
            if (!$adminNofEnabled || !$sendTo || !$sender || !$emailTemplate) {
                continue;
            }
            $sendTo = array_map('trim', explode(',', $sendTo));

            /** @var ResponseCollection $responseCollection */
            $responseCollection = $this->responsesCollectionFactory->create()
                ->addFieldToFilter('form_id', $form->getId())
                ->addFieldToFilter('admin_nof', 0);
            if (!$responseCollection->getSize()) {
                continue;
            }

            $responseInfo  = '';
            $attachedFiles = [];
            /** @var Responses $response */
            foreach ($responseCollection->getItems() as $response) {
                if ($isAttachedFiles) {
                    $attachedFiles = array_merge($this->helperData->getFileFromResponse($response), $attachedFiles);
                }
                $responseInfo .= $this->getResponseHtml($response) . '<br/>';
            }

            $templateVars = [
                'attachedFiles'   => $attachedFiles,
                'form_name'       => $form->getName(),
                'responses_count' => $responseCollection->getSize(),
                'response_info'   => $responseInfo
            ];

            try {
                $this->helperData->sendMail(
                    0,
                    $sendTo,
                    $emailTemplate,
                    $templateVars,
                    $sender,
                    true,
                    $ccEmails,
                    $bccEmails
                );
                $responseCollection->walk([$this, 'changeAdminNofStatus']);
            } catch (Exception $e) {
                $this->logger->critical($e);
            }
        }
    }

    /**
     * @param Responses $response
     *
     * @throws Exception
     */
    public function changeAdminNofStatus($response)
    {
        $response->setAdminNof(1);
        $this->responsesResource->save($response);
    }

    /**
     * @param FormCollection $formCollection
     *
     * @return FormCollection
     */
    protected function addFrequencyFilter($formCollection)
    {
        return $formCollection->addFieldToFilter('admin_nof_send_time', 'immediately');
    }

    /**
     * @param Responses $response
     *
     * @return mixed
     */
    public function getResponseHtml($response)
    {
        $resultPage = $this->resultPageFactory->create();

        return $resultPage->getLayout()
            ->createBlock(ResponseForm::class)
            ->setTemplate('Mageplaza_CustomForm::response_detail.phtml')
            ->setResponseEmail($response)
            ->toHtml();
    }
}
