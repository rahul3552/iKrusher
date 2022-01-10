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

namespace Mageplaza\ProductAttachments\Controller\File;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Helper\File as FileHelper;
use Mageplaza\ProductAttachments\Model\Config\Source\FileType;
use Mageplaza\ProductAttachments\Model\File;
use Mageplaza\ProductAttachments\Model\FileFactory as MpFileFactory;
use Mageplaza\ProductAttachments\Model\Log;
use Mageplaza\ProductAttachments\Model\LogFactory as MpLogFactory;

/**
 * Class Download
 * @package Mageplaza\ProductAttachments\Controller\File
 */
class Download extends Action
{
    const MP_PRODUCT_ATTACHMENTS_MODULE = 'Mageplaza_ProductAttachments';

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var ReadFactory
     */
    protected $_readFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * @var MpFileFactory
     */
    protected $_mpFileFactory;

    /**
     * @var MpLogFactory
     */
    protected $_mpLogFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var ResultFactory
     */
    protected $_resultRedirect;

    /**
     * Download constructor.
     *
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param StoreManagerInterface $storeManager
     * @param RawFactory $rawFactory
     * @param ReadFactory $readFactory
     * @param Filesystem $filesystem
     * @param MpFileFactory $mpFileFactory
     * @param MpLogFactory $mpLogFactory
     * @param Session $customerSession
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        StoreManagerInterface $storeManager,
        RawFactory $rawFactory,
        ReadFactory $readFactory,
        Filesystem $filesystem,
        MpFileFactory $mpFileFactory,
        MpLogFactory $mpLogFactory,
        Session $customerSession,
        Data $helperData
    ) {
        $this->_fileFactory     = $fileFactory;
        $this->_storeManager    = $storeManager;
        $this->resultRawFactory = $rawFactory;
        $this->_readFactory     = $readFactory;
        $this->_filesystem      = $filesystem;
        $this->_mpFileFactory   = $mpFileFactory;
        $this->_mpLogFactory    = $mpLogFactory;
        $this->_customerSession = $customerSession;
        $this->_helperData      = $helperData;
        $this->_resultRedirect  = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Raw|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $customerId = $this->_customerSession->isLoggedIn() ? $this->_customerSession->getCustomer()->getId() : 0;
        $fileId     = $this->getRequest()->getParam('id');
        $productId  = $this->getRequest()->getParam('product_id');

        /** @var File $file */
        $file = $this->_mpFileFactory->create()->load($fileId);
        if ($file->getCustomerLogin() && !$this->_customerSession->isLoggedIn()) {
            return $this->_redirect('noroute');
        }
        if ($file->getIsBuyer()
            && !$this->_helperData->isPurchased($file->getIsBuyer(), $file->getOrderStatuses(), $productId)) {
            return $this->_redirect('noroute');
        }
        $mediaPath     = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $directoryRead = $this->_readFactory->create($mediaPath);
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        if ((int) $file->getType() === FileType::IN_STORE) {
            $fileAbsolutePath = FileHelper::TEMPLATE_MEDIA_PATH . '/'
                . FileHelper::TEMPLATE_MEDIA_TYPE_FILE . '/' . $file->getFilePath();

            $this->_fileFactory->create(
                $file->getName(),
                null,
                DirectoryList::PUB,
                'application/octet-stream',
                $file->getSize()
            );
            $resultRaw->setContents($directoryRead->readFile($fileAbsolutePath));
        } else {
            $resultRedirect = $this->_resultRedirect->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($file->getFilePath());
            $file->setFileAction(1);

            return $resultRedirect;
        }

        /** @var Log $file */
        $log     = $this->_mpLogFactory->create();
        $logData = [
            'file_id'        => $fileId,
            'customer_id'    => $customerId,
            'product_id'     => $productId,
            'file_action'    => $file->getFileAction(),
            'store_id'       => $this->_storeManager->getStore()->getId(),
            'customer_group' => $customerId ? $this->_customerSession->getCustomer()->getGroupId() : 0
        ];
        $log->addData($logData)->save();

        return $resultRaw;
    }
}
