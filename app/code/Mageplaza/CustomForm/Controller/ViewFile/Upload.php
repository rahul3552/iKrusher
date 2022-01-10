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

namespace Mageplaza\CustomForm\Controller\ViewFile;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Helper\FileType;
use Psr\Log\LoggerInterface;
use Zend_Validate_File_Upload;

/**
 * Class Upload
 * @package Mageplaza\CustomForm\Controller\ViewFile
 */
class Upload extends Action
{
    /**
     * @var string
     */
    protected $scope = 'mpOrderAttributes';

    /**
     * @var Zend_Validate_File_Upload
     */
    protected $fileUpload;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Uploader
     */
    protected $uploaderFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @var FileType
     */
    protected $fileTypeHelper;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param Zend_Validate_File_Upload $fileUpload
     * @param LoggerInterface $logger
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param Data $data
     * @param FileType $fileTypeHelper
     */
    public function __construct(
        Context $context,
        Zend_Validate_File_Upload $fileUpload,
        LoggerInterface $logger,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        Data $data,
        FileType $fileTypeHelper
    ) {
        $this->fileUpload = $fileUpload;
        $this->logger = $logger;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->data = $data;
        $this->fileTypeHelper = $fileTypeHelper;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            if (empty($this->fileUpload->getFiles())) {
                throw new LocalizedException(__('$_FILES array is empty.'));
            }

            $fileId = key($this->fileUpload->getFiles());

            /** @var Uploader $uploader */
            $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            /** @var Read $mediaDirectory */
            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

            $now = time();
            $result = $uploader->save(
                $mediaDirectory->getAbsolutePath(Data::FILE_MEDIA_PATH) . '/' . $now
            );
            unset($result['path'], $result['tmp_name']);
            $result['file'] = $now . '/' . $result['file'];
            $result['url'] = $this->data->getTmpMediaUrl($result['file']);
            $absolutePath = $mediaDirectory->getAbsolutePath(Data::FILE_MEDIA_PATH)
                . '/' . $result['file'];
            $allowedExtensions = $this->getRequest()->getParam('allowed_extensions');
            $allowedExtensions = $allowedExtensions ? explode(',', $allowedExtensions) : [];
            $allowedMime = [];
            if (!empty($allowedExtensions)) {
                $mimeType = $this->fileTypeHelper->getMimeType($absolutePath);
                $mimeTypes = $this->fileTypeHelper->getMimeTypes();
                foreach ($allowedExtensions as $type) {
                    $allowedMime[] = $mimeTypes[$type];
                }
                if (!in_array($mimeType, $allowedMime, true)) {
                    throw new LocalizedException(__('We don\'t recognize or support this file extension type.'));
                }
            }
        } catch (Exception $e) {
            $this->logger->critical($e);
            $result = [
                'error' => __($e->getMessage()),
                'errorcode' => $e->getCode(),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}
