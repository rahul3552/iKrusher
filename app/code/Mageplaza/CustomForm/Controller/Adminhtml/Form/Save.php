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
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mageplaza\CustomForm\Model\Form as ModelForm;
use Mageplaza\CustomForm\Controller\Adminhtml\Form;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\FormFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Form as FormResource;
use Mageplaza\CustomForm\Model\ResourceModel\Responses as ResponsesResource;
use Mageplaza\CustomForm\Model\ResourceModel\Responses\CollectionFactory as ResponsesCollectionFactory;
use Mageplaza\CustomForm\Model\Responses;
use RuntimeException;

/**
 * Class Save
 * @package Mageplaza\Blog\Controller\Adminhtml\Post
 */
class Save extends Form
{
    /**
     * @var ResponsesResource
     */
    protected $responsesResource;

    /**
     * @var ResponsesCollectionFactory
     */
    protected $responsesCollectionFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FormFactory $formFactory
     * @param FormResource $formResource
     * @param ResponsesResource $responsesResource
     * @param ResponsesCollectionFactory $responsesCollectionFactory
     * @param TimezoneInterface $timezone
     * @param DateTime $date
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FormFactory $formFactory,
        FormResource $formResource,
        ResponsesResource $responsesResource,
        ResponsesCollectionFactory $responsesCollectionFactory,
        TimezoneInterface $timezone,
        DateTime $date,
        Data $helperData
    ) {
        $this->responsesResource          = $responsesResource;
        $this->responsesCollectionFactory = $responsesCollectionFactory;
        $this->timezone                   = $timezone;
        $this->date                       = $date;

        parent::__construct($context, $coreRegistry, $formFactory, $formResource, $helperData);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPost('form')) {
            $validate = true;

            if (isset($data['admin_nof_send_to'])
                && !empty($data['admin_nof_send_to'])
                && $data['admin_nof_send_to'] !== 'mp-use-config'
            ) {
                $listEmailTo = explode(',', $data['admin_nof_send_to']);

                foreach ($listEmailTo as $emailTo) {
                    if (!filter_var($emailTo, FILTER_VALIDATE_EMAIL)) {
                        $validate = false;
                        $this->messageManager->addErrorMessage(__('Invalid email format in Admin Notification `Send To`'));
                    }
                }
            }

            /** @var ModelForm $form */
            $form = $this->initForm();

            if (!$form->getIsUniqueFormToStores($data['store_ids'], $data['identifier'])) {
                $validate = false;
                $this->messageManager->addErrorMessage(__('A custom form identifier with the same properties already exists in the selected store.'));
            }

            if (!$this->validateDate($data)) {
                $validate = false;
            }

            if ($validate) {
                try {
                    $data = $this->prepareData($data);
                    if ($this->checkEmailPlaningChange($data, $form)) {
                        $responsesCollection = $this->responsesCollectionFactory->create()
                            ->addFieldToFilter('form_id', $form->getId());
                        $responsesCollection->walk([$this, 'changeResponseStatus']);
                    }

                    $form->addData($data);
                    $this->formResource->save($form);

                    $this->messageManager->addSuccessMessage(__('The form has been saved.'));
                    $this->_getSession()->setData('mageplaza_custom_form_form_data', false);

                    if ($this->getRequest()->getParam('back')) {
                        $resultRedirect->setPath('*/*/edit', ['id' => $form->getId(), '_current' => true]);
                    } else {
                        $resultRedirect->setPath('*/*/');
                    }

                    return $resultRedirect;
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Form.'));
                }
                $this->_getSession()->setData('mageplaza_custom_form_form_data', $data);
            }

            $resultRedirect->setPath('*/*/edit', ['id' => $form->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    protected function validateDate($data)
    {
        if ($data['valid_from_date'] && $data['valid_to_date']
            && strtotime($data['valid_from_date']) > strtotime($data['valid_to_date'])) {
            $this->messageManager->addErrorMessage(__('Something went wrong when saving the Form. Please review the values of the Valid From Date, Valid To Date fields.'));

            return false;
        }

        return true;
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws LocalizedException
     */
    protected function prepareData($data)
    {
        $customForm = isset($data['page']) ? $data['page'] : [];
        if (isset($customForm['<%-_data_parentName_%>'])) {
            unset($customForm['<%-_data_parentName_%>']);
        }
        if (isset($customForm['<%- data._id %>'])) {
            unset($customForm['<%- data._id %>']);
        }
        $data['custom_form'] = $customForm;

        if (!isset($data['email_planing'])) {
            $data['email_planing'] = [];
        } elseif (isset($data['email_planing']['__empty'])) {
            unset($data['email_planing']['__empty']);
        }

        if (isset($data['valid_from_date']) && $data['valid_from_date']) {
            try {
                $data['valid_from_date'] = $this->timezone->convertConfigTimeToUtc($data['valid_from_date']);
            } catch (Exception $e) {
                $data['valid_from_date'] = $this->timezone->convertConfigTimeToUtc($this->date->date());
            }
        }

        if (isset($data['valid_to_date']) && $data['valid_to_date']) {
            try {
                $data['valid_to_date'] = $this->timezone->convertConfigTimeToUtc($data['valid_to_date']);
            } catch (Exception $e) {
                $data['valid_to_date'] = $this->timezone->convertConfigTimeToUtc($this->date->date());
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param ModelForm $form
     *
     * @return int
     */
    private function checkEmailPlaningChange($data, $form)
    {
        $oldPlans = $form->getEmailPlaning() ? Data::jsonDecode($form->getEmailPlaning()) : [];
        $newPlans = isset($data['email_planing']) ? $data['email_planing'] : [];

        if ($oldPlans === $newPlans) {
            return 0;
        }

        if (count($oldPlans) !== count($newPlans)) {
            return 1;
        }
        $result = 0;
        foreach ($oldPlans as $key => $plan) {
            if (!isset($newPlans[$key])) {
                $result = 1;
                break;
            }
            if ($newPlans[$key]['status'] !== $plan['status']
                || $newPlans[$key]['template'] !== $plan['template']
                || $newPlans[$key]['send_after'] !== $plan['send_after']
            ) {
                $result = 1;
                break;
            }
        }

        return $result;
    }

    /**
     * @param Responses $response
     *
     * @throws AlreadyExistsException
     */
    public function changeResponseStatus($response)
    {
        $response->setIsComplete(0);
        $this->responsesResource->save($response);
    }
}
