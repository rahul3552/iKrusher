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
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\Form as CustomFormModel;
use Mageplaza\CustomForm\Model\FormFactory as CustomFormFactory;

/**
 * Class Views
 * @package Mageplaza\CustomForm\Controller\CustomForm
 */
class Views extends Action
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var CustomFormFactory
     */
    protected $customFormFactory;

    /**
     * Views constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Data $helperData
     * @param CustomFormFactory $customFormFactory
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Data $helperData,
        CustomFormFactory $customFormFactory
    ) {
        $this->storeManager      = $storeManager;
        $this->helperData        = $helperData;
        $this->customFormFactory = $customFormFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        try {
            /** @var CustomFormModel $customForm */
            $customForm = $this->loadCustomForm();

            if ($customForm->getId()) {
                $customForm->setViews($customForm->getViews() + 1);
                $customForm->save();
            }

            return $this->getResponse()->representJson(Data::jsonEncode([
                'message' => __('The Form has been viewed.'),
                'success' => true
            ]));
        } catch (Exception $e) {
            return $this->getResponse()->representJson(Data::jsonEncode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
        }
    }

    /**
     * @return CustomFormModel
     */
    public function loadCustomForm()
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (Exception $e) {
            $storeId = Store::DEFAULT_STORE_ID;
        }

        /** @var CustomFormModel $customForm */
        $customForm = $this->customFormFactory->create();
        $identifier = $this->getRequest()->getParam('identifier');
        $customForm = $customForm->getFilterByStoreId($identifier, $storeId);

        return $customForm;
    }
}
