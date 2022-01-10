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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Model\Config\Source\FileAction;
use Mageplaza\ProductAttachments\Model\Config\Source\FileType;
use Mageplaza\ProductAttachments\Model\File;
use Mageplaza\ProductAttachments\Model\FileFactory as MpFileFactory;
use Mageplaza\ProductAttachments\Model\Log;
use Mageplaza\ProductAttachments\Model\LogFactory as MpLogFactory;

/**
 * Class ViewOnline
 * @package Mageplaza\ProductAttachments\Controller\File
 */
class ViewOnline extends Action
{
    /**
     * @var ResultFactory
     */
    protected $_resultRedirect;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

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
     * ViewOnline constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param MpFileFactory $mpFileFactory
     * @param MpLogFactory $mpLogFactory
     * @param Session $customerSession
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        MpFileFactory $mpFileFactory,
        MpLogFactory $mpLogFactory,
        Session $customerSession,
        Data $helperData
    ) {
        $this->_storeManager    = $storeManager;
        $this->_resultRedirect  = $context->getResultFactory();
        $this->_mpFileFactory   = $mpFileFactory;
        $this->_mpLogFactory    = $mpLogFactory;
        $this->_customerSession = $customerSession;
        $this->_helperData      = $helperData;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultFactory|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $customerId = $this->_customerSession->isLoggedIn() ? $this->_customerSession->getCustomer()->getId() : 0;

        $fileId            = $this->getRequest()->getParam('id');
        $productId         = $this->getRequest()->getParam('product_id');
        $viewableExtension = [
            'doc',
            'docx',
            'xls',
            'xlsx',
            'ppt',
            'pptx',
            'ods',
            'odt',
            'odp',
            'csv',
            'rtf',
            'txt',
            'png',
            'tif',
            'tiff',
            'pdf',
            'jpg',
            'jpeg'
        ];
        /** @var File $file */
        $file = $this->_mpFileFactory->create()->load($fileId);
        if ($file->getCustomerLogin() && !$this->_customerSession->isLoggedIn()) {
            return $this->_redirect('noroute');
        }
        if ($file->getIsBuyer()
            && !$this->_helperData->isPurchased($file->getIsBuyer(), $file->getOrderStatuses(), $productId)) {
            return $this->_redirect('noroute');
        }

        $resultRedirect = $this->_resultRedirect->create(ResultFactory::TYPE_REDIRECT);

        if ((int) $file->getType() === FileType::IN_STORE) {
            $fileDirPath = $this->_helperData->getFileUrl($file->getFilePath());

            $fileExtension = pathinfo($file->getName(), PATHINFO_EXTENSION);
            $fileAction    = in_array($fileExtension, $viewableExtension, true)
                ? $file->getFileAction() : FileAction::VIEWONLINE;

            /** @var ResultFactory $resultRedirect */
            $resultRedirect->setUrl($fileDirPath);
        } else {
            $resultRedirect->setUrl($file->getFilePath());
            $fileAction = $file->getFileAction();
        }

        /** @var Log $file */
        $log     = $this->_mpLogFactory->create();
        $logData = [
            'file_id'        => $fileId,
            'customer_id'    => $customerId,
            'product_id'     => $productId,
            'file_action'    => $fileAction,
            'store_id'       => $this->_storeManager->getStore()->getId(),
            'customer_group' => $customerId ? $this->_customerSession->getCustomer()->getGroupId() : 0
        ];
        $log->addData($logData)->save();

        return $resultRedirect;
    }
}
