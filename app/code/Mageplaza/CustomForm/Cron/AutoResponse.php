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

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
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

/**
 * Class AutoResponse
 * @package Mageplaza\CustomForm\Cron
 */
class AutoResponse
{
    /**
     * Customer repository
     *
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

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
     * AutoResponse constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param FormCollectionFactory $formCollectionFactory
     * @param ResponsesCollectionFactory $responsesCollectionFactory
     * @param ResponsesResource $responsesResource
     * @param PageFactory $resultPageFactory
     * @param Data $helperData
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        FormCollectionFactory $formCollectionFactory,
        ResponsesCollectionFactory $responsesCollectionFactory,
        ResponsesResource $responsesResource,
        PageFactory $resultPageFactory,
        Data $helperData
    ) {
        $this->customerRepository         = $customerRepository;
        $this->formCollectionFactory      = $formCollectionFactory;
        $this->responsesCollectionFactory = $responsesCollectionFactory;
        $this->responsesResource          = $responsesResource;
        $this->helperData                 = $helperData;
        $this->resultPageFactory          = $resultPageFactory;
    }

    /**
     * @throws AlreadyExistsException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        if (!$this->helperData->isEnabled()) {
            return;
        }
        /** @var FormCollection $formCollection */
        $formCollection = $this->formCollectionFactory->create()
            ->addFieldToFilter('status', 1);
        /** @var Form $form */
        foreach ($formCollection as $form) {
            $emailPlaning      = $form->getEmailPlaning() ? Data::jsonDecode($form->getEmailPlaning()) : [];
            $autoResEnabled    = $form->getAutoResEnabled();
            $isAttachedFiles   = $form->getAutoResAttachedFiles();
            $emailAddressField = $form->getAutoResEmailAddress();

            if ($autoResEnabled === Data::USE_CONFIG_VAL) {
                $autoResEnabled = $this->helperData->getCustomerNofEnabled();
            }

            if ($isAttachedFiles === Data::USE_CONFIG_VAL) {
                $isAttachedFiles = $this->helperData->getCustomerAttachedFile();
            }

            if (!$autoResEnabled || !$emailAddressField) {
                continue;
            }

            /** @var ResponseCollection $responseCollection */
            $responseCollection = $this->responsesCollectionFactory->create()
                ->addFieldToFilter('form_id', $form->getId())
                ->addFieldToFilter('is_complete', 0);
            /** @var Responses $response */
            foreach ($responseCollection as $response) {
                $attachedFiles = $isAttachedFiles ? $this->helperData->getFileFromResponse($response) : [];
                $completedPlan = $response->getEmailPlaning() ? explode(',', $response->getEmailPlaning()) : [];
                $isComplete    = 1;
                $customerName  = '';
                $formData      = $response->getFormData() ? Data::jsonDecode($response->getFormData()) : [];
                $sendTo        = '';
                $responseInfo  = $this->getResponseHtml($response);

                if ($customerId = $response->getCustomerId()) {
                    $customer     = $this->customerRepository->getById($customerId);
                    $customerName = $customer->getFirstname();
                }

                list($pageId, $fieldGroupId, $fieldId) = explode('-', $emailAddressField);

                if (isset($formData[$pageId]['fieldGroups'][$fieldGroupId]['fields'][$fieldId])) {
                    $sendTo = trim($formData[$pageId]['fieldGroups'][$fieldGroupId]['fields'][$fieldId]);
                }

                if (empty($emailPlaning)) {
                    $sendTo = explode(',', $sendTo);
                    $this->helperData->sendMail(
                        $response->getStoreIds(),
                        $sendTo,
                        $this->helperData->getCustomerNofEmailTemplate($response->getStoreIds()),
                        [
                            'attachedFiles' => $attachedFiles,
                            'form_name'     => $form->getName(),
                            'customer_name' => $customerName,
                            'response_info' => $responseInfo
                        ],
                        $this->helperData->getCustomerNofSender($response->getStoreIds())
                    );
                    $response->setIsComplete(1);
                    $this->responsesResource->save($response);
                    continue;
                }

                foreach ($emailPlaning as $planId => $plan) {
                    if (!$plan['status'] || in_array($planId, $completedPlan, true)) {
                        continue;
                    }
                    $sendAfter     = $plan['send_after'] ?: 'now';
                    $submitTime    = $response->getCreatedAt();
                    $emailTemplate = $plan['template'];
                    $sender        = $form->getAutoResSender();

                    if ($sender === 'mp-use-config') {
                        $sender = $this->helperData->getCustomerNofSender($response->getStoreIds());
                    }

                    if (!$emailTemplate || !$sender || !$sendTo) {
                        continue;
                    }

                    if (strtotime($submitTime) + strtotime($sendAfter) < time() * 2) {
                        $sendTo = explode(',', $sendTo);

                        $this->helperData->sendMail(
                            $response->getStoreIds(),
                            $sendTo,
                            $emailTemplate,
                            [
                                'attachedFiles' => $attachedFiles,
                                'form_name'     => $form->getName(),
                                'customer_name' => $customerName,
                                'response_info' => $responseInfo
                            ],
                            $sender
                        );
                        $completedPlan[] = $planId;
                    } else {
                        $isComplete = 0;
                    }
                }
                $response->setIsComplete($isComplete);
                $response->setEmailPlaning(implode(',', $completedPlan));
                $this->responsesResource->save($response);
            }
        }
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
