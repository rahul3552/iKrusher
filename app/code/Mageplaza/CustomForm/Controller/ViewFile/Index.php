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
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\MediaStorage\Helper\File\Storage;
use Mageplaza\CustomForm\Helper\Data;

/**
 * Class Index
 * @package Mageplaza\CustomForm\Controller\ViewFile
 */
class Index extends Action
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param Filesystem $filesystem
     * @param File $file
     * @param Storage $storage
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        Filesystem $filesystem,
        File $file,
        Storage $storage,
        Data $helperData
    ) {
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->storage = $storage;
        $this->helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Raw|ResultInterface
     * @throws NotFoundException
     * @throws Exception
     */
    public function execute()
    {
        $file = base64_decode($this->getRequest()->getParam('file'));
        $directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName = Data::FILE_MEDIA_PATH . '/' . ltrim($file, '/');
        $path = $directory->getAbsolutePath($fileName);

        if (mb_strpos($path, '..') !== false
            || (!$directory->isFile($fileName) && !$this->storage->processStorageFile($path))) {
            throw new NotFoundException(__('Page not found.'));
        }

        $name = $this->file->getPathInfo($path)['basename'];

        return $this->fileFactory->create(
            $name,
            ['type' => 'filename', 'value' => $fileName],
            DirectoryList::MEDIA
        );
    }
}
