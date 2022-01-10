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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page as PageResultModel;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\CustomForm\Model\Form as ModelForm;
use Mageplaza\CustomForm\Controller\Adminhtml\Form;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\FormFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Form as FormResource;

/**
 * Class Edit
 * @package Mageplaza\CustomForm\Controller\Adminhtml\Form
 */
class Edit extends Form
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FormFactory $formFactory
     * @param FormResource $formResource
     * @param PageFactory $resultPageFactory
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FormFactory $formFactory,
        FormResource $formResource,
        PageFactory $resultPageFactory,
        Data $helperData
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context, $coreRegistry, $formFactory, $formResource, $helperData);
    }

    /**
     * @return PageResultModel|ResponseInterface|Redirect|ResultInterface|Page
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var ModelForm $form */
        $form = $this->initForm();
        if (!$form) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('mageplaza_custom_form_form_data', true);
        if (!empty($data)) {
            $form->setData($data);
        }

        $this->coreRegistry->register('mageplaza_custom_form_form', $form);

        /** @var PageResultModel|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_CustomForm::form');
        $resultPage->getConfig()->getTitle()->set(__('Forms'));

        $title = $form->getId() ? $form->getName() : __('New Form');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
