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

namespace Mageplaza\CustomForm\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\Form as ModelForm;
use Mageplaza\CustomForm\Model\FormFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Form as FormResource;

/**
 * Class Form
 * @package Mageplaza\CustomForm\Controller\Adminhtml
 */
abstract class Form extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mageplaza_CustomForm::form';

    /**
     * Post Factory
     *
     * @var FormFactory
     */
    public $formFactory;

    /**
     * @var FormResource
     */
    public $formResource;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FormFactory $formFactory
     * @param FormResource $formResource
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FormFactory $formFactory,
        FormResource $formResource,
        Data $helperData
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->formFactory  = $formFactory;
        $this->formResource = $formResource;
        $this->helperData   = $helperData;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|ModelForm
     * @throws LocalizedException
     */
    protected function initForm($register = false)
    {
        $formId = $this->getRequest()->getParam('id');

        /** @var ModelForm $form */
        $form = $this->formFactory->create();

        if ($formId) {
            if ($formId === 'copy') {
                $data = $this->_session->getCopyData();
                $form->setData($data)->setId(null);
                if (!$form->getIsUniqueFormToStores($data['store_ids'], $data['identifier'])) {
                    $identifier = $this->helperData->generateUrlKey($data['identifier']);
                    $form->setIdentifier($identifier);
                }
            } else {
                $this->formResource->load($form, (int) $formId);
                if (!$form->getId()) {
                    $this->messageManager->addErrorMessage(__('This form no longer exists.'));

                    return false;
                }
            }
        }

        if ($register) {
            $this->coreRegistry->register('mageplaza_custom_form_form', $form);
        }

        return $form;
    }
}
