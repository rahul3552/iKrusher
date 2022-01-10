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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Mageplaza\CustomForm\Controller\Adminhtml\Form as AbstractForm;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\FormFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Form as FormResource;

/**
 * Class ResponsesDetail
 * @package Mageplaza\CustomForm\Controller\Adminhtml\Form
 */
class ResponsesDetail extends AbstractForm
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * ResponsesDetail constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FormFactory $formFactory
     * @param FormResource $formResource
     * @param LayoutFactory $resultLayoutFactory
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FormFactory $formFactory,
        FormResource $formResource,
        LayoutFactory $resultLayoutFactory,
        Data $helperData
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context, $coreRegistry, $formFactory, $formResource, $helperData);
    }

    /**
     * @return ResponseInterface|ResultInterface|Layout
     * @throws LocalizedException
     */
    public function execute()
    {
        $this->initForm(true);

        return $this->resultLayoutFactory->create();
    }
}
