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

namespace Mageplaza\ProductAttachments\Block\Adminhtml\File\Edit\Tab\File;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Mageplaza\ProductAttachments\Helper\Data as HelperData;
use Mageplaza\ProductAttachments\Helper\File as HelperFile;
use Mageplaza\ProductAttachments\Model\File;

/**
 * Class Icon
 * @package Mageplaza\ProductAttachments\Block\Adminhtml\File\Edit\Tab\File
 */
class Icon extends Template
{
    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * @var HelperFile
     */
    public $helperFile;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Icon constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param HelperData $helperData
     * @param HelperFile $helperFile
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        HelperData $helperData,
        HelperFile $helperFile,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->helperData = $helperData;
        $this->helperFile = $helperFile;

        parent::__construct($context, $data);
    }

    /**
     * @return File
     */
    public function getCurrentFile()
    {
        /** @var File $file */
        $file = $this->_coreRegistry->registry('mageplaza_productattachments_file');

        return $file;
    }
}
