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

namespace Mageplaza\ProductAttachments\Helper;

use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\Media;

/**
 * Class File
 * @package Mageplaza\ProductAttachments\Helper
 */
class File extends Media
{
    const CONFIG_MODULE_PATH       = 'productattachments';
    const TEMPLATE_MEDIA_PATH      = 'mageplaza/product_attachments';
    const TEMPLATE_MEDIA_TYPE_FILE = 'attachment_file';
    const TEMPLATE_MEDIA_TYPE_ICON = 'file_icons';

    /**
     * @var UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * File constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param AdapterFactory $imageFactory
     *
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        AdapterFactory $imageFactory
    ) {
        $this->_uploaderFactory = $uploaderFactory;

        parent::__construct($context, $objectManager, $storeManager, $filesystem, $uploaderFactory, $imageFactory);
    }

    /**
     * @param array $data
     * @param string $fileName
     * @param string $type
     * @param null $oldFile
     *
     * @return $this
     * @throws FileSystemException
     */
    public function uploadFile(&$data, $fileName, $type = '', $oldFile = null)
    {
        if (isset($data[$fileName]['delete']) && $data[$fileName]['delete']) {
            if ($oldFile) {
                $this->removeFile($oldFile, $type);
            }
            $data['file_path'] = '';
        } else {
            try {
                $maxFileSizeUpload = (float) $this->getLimitFileSizeUpload() ? : Data::MAX_SIZE_FILE_UPLOAD;
                $uploader          = $this->_uploaderFactory->create(['fileId' => $fileName]);
                $fileSize          = (float) number_format($uploader->getFileSize() / (1024 ** 2), 2);

                if ($maxFileSizeUpload && $fileSize > $maxFileSizeUpload) {
                    $data['error']     = __('The attachments you selected have exceeded the allowed limit. Please upload files smaller than %1MB.', $maxFileSizeUpload);
                    $data['file_path'] = '';
                    return $this;
                }

                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $path         = $this->getBaseMediaPath($type);
                $uploadedFile = $uploader->save(
                    $this->mediaDirectory->getAbsolutePath($path)
                );
                if ($oldFile) {
                    $this->removeFile($oldFile, $type);
                }
                $data['file_path'] = $this->_prepareFile($uploadedFile['file']);
                $data['size']      = $uploadedFile['size'];
            } catch (Exception $e) {
                $data['error']     = $e->getMessage();
                $data['file_path'] = isset($data['file_path']['value']) ? $data['file_path']['value'] : '';
            }
        }

        return $this;
    }

    /**
     * @param string $uploadedFile
     * @param string $type
     *
     * @return $this
     * @throws FileSystemException
     */
    public function removeFile($uploadedFile, $type)
    {
        $file = $this->getMediaPath($uploadedFile, $type);
        if ($this->mediaDirectory->isFile($file)) {
            $this->mediaDirectory->delete($file);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLimitFileSizeUpload()
    {
        return $this->getConfigGeneral('file_size_limit');
    }
}
