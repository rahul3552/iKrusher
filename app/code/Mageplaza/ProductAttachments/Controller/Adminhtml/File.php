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

namespace Mageplaza\ProductAttachments\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mageplaza\ProductAttachments\Model\FileFactory;

/**
 * Class File
 * @package Mageplaza\ProductAttachments\Controller\Adminhtml
 */
abstract class File extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mageplaza_ProductAttachments::file';

    /**
     * File model factory
     *
     * @var FileFactory
     */
    public $fileFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * File constructor.
     *
     * @param FileFactory $fileFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        FileFactory $fileFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->fileFactory = $fileFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|\Mageplaza\ProductAttachments\Model\File
     */
    protected function initFile($register = false)
    {
        $fileId = (int)$this->getRequest()->getParam('id');

        /** @var \Mageplaza\ProductAttachments\Model\File $file */
        $file = $this->fileFactory->create();

        if ($fileId) {
            $file->load($fileId);
            if (!$file->getId()) {
                $this->messageManager->addErrorMessage(__('This file no longer exists.'));

                return false;
            }
        }
        if ($register) {
            $this->coreRegistry->register('mageplaza_productattachments_file', $file);
        }

        return $file;
    }
}
