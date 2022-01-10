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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\CustomForm\Model\Form;
use Mageplaza\CustomForm\Model\FormFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Form as FormResource;
use RuntimeException;

/**
 * Class InlineEdit
 * @package Mageplaza\CustomForm\Controller\Adminhtml\Form
 */
class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var FormResource
     */
    protected $formResource;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param FormFactory $formFactory
     * @param FormResource $formResource
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        FormFactory $formFactory,
        FormResource $formResource
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->formFactory = $formFactory;
        $this->formResource = $formResource;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $formItems = $this->getRequest()->getParam('items', []);
        if (!empty($formItems) && !$this->getRequest()->getParam('isAjax')) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $key = array_keys($formItems);
        $formId = !empty($key) ? (int)$key[0] : '';
        /** @var Form $form */
        $form = $this->formFactory->create();
        $this->formResource->load($form, $formId);
        try {
            $formData = $formItems[$formId];
            $form->addData($formData);
            $this->formResource->save($form);
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithFormId($form, $e->getMessage());
            $error = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithFormId(
                $form,
                __('Something went wrong while saving the Form. %1', $e->getMessage())
            );
            $error = true;
        }

        return $resultJson->setData(compact('messages', 'error'));
    }

    /**
     * Add Profile id to error message
     *
     * @param Form $form
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithFormId(Form $form, $errorText)
    {
        return '[Form ID: ' . $form->getId() . '] ' . $errorText;
    }
}
