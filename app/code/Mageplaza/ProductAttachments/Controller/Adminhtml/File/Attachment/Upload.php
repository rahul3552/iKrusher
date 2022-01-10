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

namespace Mageplaza\ProductAttachments\Controller\Adminhtml\File\Attachment;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\File\Size;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\Uploader;
use Mageplaza\ProductAttachments\Block\Adminhtml\Product\Form\Attachments;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Helper\File;
use Mageplaza\ProductAttachments\Model\Config\Source\Icon;

/**
 * Class Upload
 * @package Mageplaza\ProductAttachments\Controller\Adminhtml\File\Attachment
 */
class Upload extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Catalog::products';

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var Size
     */
    protected $_fileSize;

    /**
     * @var File
     */
    protected $_helperFile;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var array
     */
    protected $_iconList;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Size $fileSize
     * @param File $helperFile
     * @param Data $helperData
     * @param Icon $iconList
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Size $fileSize,
        File $helperFile,
        Data $helperData,
        Icon $iconList
    ) {
        $this->_resultPageFactory = $pageFactory;
        $this->_fileSize          = $fileSize;
        $this->_helperFile        = $helperFile;
        $this->_helperData        = $helperData;
        $this->_iconList          = $iconList->toOptionArray();

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $page         = $this->_resultPageFactory->create();
        $layout       = $page->getLayout();
        $currentDate  = $this->getRequest()->getParam('value_id');
        $position     = (int) $this->getRequest()->getParam('position');
        $type         = $this->getRequest()->getParam('type');
        $maxImageSize = $this->_fileSize->getMaxFileSizeInMb();

        if ($type === '0') {
            /** Upload file to media directory */
            $uploader = $this->_objectManager->create(
                Uploader::class,
                ['fileId' => 'image']
            );

            $maxFileSizeUpload = (float) $this->_helperData->getLimitFileSizeUpload() ? : Data::MAX_SIZE_FILE_UPLOAD;
            $fileSize          = (float) number_format($uploader->getFileSize() / (1024 ** 2), 2);

            if ($maxFileSizeUpload && $fileSize > $maxFileSizeUpload) {
                return $this->getResponse()->representJson(Data::jsonEncode([
                    'status'    => false,
                    'errorSize' => __('Make sure your file isn\'t more than %1MB.', $maxFileSizeUpload)
                ]));
            }

            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            /** @var Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get(Filesystem::class)
                ->getDirectoryRead(DirectoryList::MEDIA);
            try {
                $result = $uploader->save(
                    $mediaDirectory->getAbsolutePath(
                        $this->_helperFile->getBaseMediaPath(FILE::TEMPLATE_MEDIA_TYPE_FILE)
                    )
                );
            } catch (Exception $e) {
                return $this->getResponse()->representJson(Data::jsonEncode([
                    'status'    => false,
                    'error'     => $e->getMessage(),
                    'errorcode' => $e->getCode(),
                    'errorSize' => __('Make sure your file isn\'t more than %1M.', $maxImageSize)
                ]));
            }

            $extension  = strrchr($result['name'], '.');
            $fileLabel  = str_replace($extension, '', $result['name']);
            $fileName   = $result['name'];
            $filePath   = $result['file'];
            $fileSize   = $result['size'];
            $fileAction = $this->_helperData->getDefaultValueConfig('customer_action') ?: 0;
        } else {
            $fileName   = $this->getRequest()->getParam('name');
            $extension  = strrchr($fileName, '.');
            $fileLabel  = $this->getRequest()->getParam('label');
            $filePath   = $this->getRequest()->getParam('file_path');
            $fileSize   = 0;
            $fileAction = '1';
        }

        /** Get file extension icon */

        $extension = str_replace('.', '', $extension);

        /** Get file's icon path */
        $iconPath = '';
        array_shift($this->_iconList);
        foreach ($this->_iconList as $icon) {
            if ($extension === $icon['label']) {
                $iconPath = $icon['value'];
            }
        }

        /** Set return data to file gallery */
        $data = [
            'file_id'        => $currentDate,
            'label'          => $fileLabel,
            'name'           => $fileName,
            'status'         => 1,
            'store_ids'      => $this->_helperData->getDefaultValueConfig('store_view') ?: 0,
            'customer_group' => $this->_helperData->getDefaultValueConfig('customer_group') ?: 0,
            'file_path'      => $filePath,
            'file_icon_path' => $iconPath,
            'customer_login' => $this->_helperData->getDefaultValueConfig('is_login') ?: 0,
            'is_buyer'       => $this->_helperData->getDefaultValueConfig('is_buyer') ?: 0,
            'order_statuses' => $this->_helperData->getDefaultValueConfig('order_status') ?: '',
            'file_action'    => $fileAction,
            'type'           => $type,
            'priority'       => 99,
            'size'           => $fileSize,
            'is_new'         => 1,
            'url'            => $iconPath
                ? $this->_helperData->getImageUrl($iconPath)
                : $this->_helperData->getDefaultIconUrl(),
            'position'       => $position + 1
        ];
        $this->_eventManager->dispatch(
            'mpattachments_product_gallery_upload_file_after',
            ['result' => $data, 'action' => $this]
        );

        $isGrid   = $this->getRequest()->getParam('is_grid') ? true : false;
        $response = [
            'faq_list' => $layout->createBlock(Attachments::class)
                ->setTemplate('Mageplaza_ProductAttachments::group/newfile/attachments.phtml')
                ->setFileData($data)->setIsGrid($isGrid)->toHtml(),
            'status'   => true
        ];

        return $this->getResponse()->representJson(Data::jsonEncode($response));
    }
}
