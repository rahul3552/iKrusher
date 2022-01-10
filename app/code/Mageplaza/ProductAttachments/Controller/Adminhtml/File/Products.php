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
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Controller\Adminhtml\File;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\ProductAttachments\Controller\Adminhtml\File;
use Mageplaza\ProductAttachments\Model\FileFactory;

/**
 * Class Products
 * @package Mageplaza\ProductAttachments\Controller\Adminhtml\File
 */
class Products extends File
{
    /**
     * JS helper
     *
     * @var Js
     */
    protected $_jsonHelper;

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * Products constructor.
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param JsonHelper $jsonHelper
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        PageFactory $pageFactory,
        JsonHelper $jsonHelper,
        FileFactory $fileFactory
    ) {
        parent::__construct($fileFactory, $coreRegistry, $context);

        $this->_jsonHelper = $jsonHelper;
        $this->_pageFactory = $pageFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $page = $this->_pageFactory->create();
        $html = $page->getLayout()
            ->createBlock(\Mageplaza\ProductAttachments\Block\Adminhtml\File\Edit\Tab\Renderer\Products::class)
            ->toHtml();
        if ($this->getRequest()->getParam('loadGrid')) {
            $html = $this->_jsonHelper->jsonEncode($html);
        }

        return $this->getResponse()->representJson($html);
    }
}
