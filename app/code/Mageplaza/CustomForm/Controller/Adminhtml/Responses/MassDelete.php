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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\Form;
use Mageplaza\CustomForm\Model\FormFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Form as FormResource;
use Mageplaza\CustomForm\Model\ResourceModel\Responses\CollectionFactory;

/**
 * Class MassDelete
 * @package Mageplaza\CustomForm\Controller\Adminhtml\Responses
 */
class MassDelete extends Action
{
    /**
     * Mass Action Filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * Collection Factory
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var FormResource
     */
    protected $formResource;

    /**
     * MassDelete constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param FormFactory $formFactory
     * @param FormResource $formResource
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FormFactory $formFactory,
        FormResource $formResource
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->formFactory = $formFactory;
        $this->formResource = $formResource;

        parent::__construct($context);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create()->setOrder('form_id'));

        $deletedCount = 0;
        $formId = null;
        $responseIds = [];
        $customForm = $this->formFactory->create();
        foreach ($collection as $item) {
            if ($item->getFormId() !== $formId) {
                $this->reindexResponsesSummary($responseIds, $customForm);
                $responseIds = [];
                $formId = $item->getFormId();
                $customForm = $this->formFactory->create();
                $this->formResource->load($customForm, $formId);
            }
            try {
                $responseIds[] = $item->getId();
                $item->delete();
                $deletedCount++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    __('Something went wrong while deleting for response %1.', $item->getId())
                );
            }
        }
        $this->reindexResponsesSummary($responseIds, $customForm);

        if ($deletedCount) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $deletedCount));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param array $responseIds
     * @param Form $customForm
     *
     * @throws AlreadyExistsException
     */
    private function reindexResponsesSummary($responseIds, $customForm)
    {
        if (!empty($responseIds) && $customForm->getId()) {
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
                            foreach ($responseIds as $responseId) {
                                if (isset($field[$responseId])) {
                                    unset($field[$responseId]);
                                }
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
}
