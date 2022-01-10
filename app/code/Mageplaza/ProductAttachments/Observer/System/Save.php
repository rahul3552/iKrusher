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

namespace Mageplaza\ProductAttachments\Observer\System;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\ProductAttachments\Model\Config\Source\Icon;
use Mageplaza\ProductAttachments\Model\FileFactory;

/**
 * Class Save
 * @package Mageplaza\ProductAttachments\Observer\System
 */
class Save implements ObserverInterface
{
    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var Icon
     */
    protected $_iconList;

    /**
     * Save constructor.
     *
     * @param FileFactory $fileFactory
     * @param Icon $iconList
     */
    public function __construct(
        FileFactory $fileFactory,
        Icon $iconList
    ) {
        $this->_fileFactory = $fileFactory;
        $this->_iconList = $iconList;
    }

    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $icon = [];
        $fileCollection = $this->_fileFactory->create()->getCollection();
        $iconList = $this->_iconList->toOptionArray();
        array_shift($iconList);
        foreach ($iconList as $item) {
            $icon[$item['label']] = $item['value'];
        }
        foreach ($fileCollection as $file) {
            $fileExtension = pathinfo($file->getName(), PATHINFO_EXTENSION);
            $fileIconExtension = null;
            if (array_key_exists($fileExtension, $icon)) {
                $fileIconExtension = $icon[$fileExtension];
            }
            if ($fileIconExtension) {
                $file->setFileIconPath($fileIconExtension)->save();
            }
        }
    }
}
