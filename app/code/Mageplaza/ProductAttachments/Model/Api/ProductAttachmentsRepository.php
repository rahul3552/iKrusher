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

namespace Mageplaza\ProductAttachments\Model\Api;

use Exception;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Webapi\Rest\Response as RestResponse;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ProductAttachments\Api\ProductAttachmentsRepositoryInterface;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Helper\File as FileHelper;
use Mageplaza\ProductAttachments\Model\Config\Source\FileType;
use Mageplaza\ProductAttachments\Model\Config\Source\Status;
use Mageplaza\ProductAttachments\Model\File;
use Mageplaza\ProductAttachments\Model\FileFactory as MpFileFactory;
use Mageplaza\ProductAttachments\Model\Log;
use Mageplaza\ProductAttachments\Model\LogFactory as MpLogFactory;

/**
 * Class ProductAttachmentsRepository
 * @package Mageplaza\ProductAttachments\Model\Api
 */
class ProductAttachmentsRepository implements ProductAttachmentsRepositoryInterface
{
    /**
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * @var ReadFactory
     */
    protected $_readFactory;

    /**
     * @var MpLogFactory
     */
    protected $_mpLogFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var MpFileFactory
     */
    protected $_mpFileFactory;

    /**
     * @var RestResponse
     */
    private $_response;

    /**
     * @var array
     */
    private $mimet = [
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    ];
    /**
     * @var Data
     */
    private $helperData;

    /**
     * ProductAttachmentsRepository constructor.
     *
     * @param Filesystem $filesystem
     * @param ReadFactory $readFactory
     * @param MpLogFactory $logFactory
     * @param CustomerFactory $customerFactory
     * @param RestResponse $response
     * @param Data $helperData
     * @param MpFileFactory $mpFileFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Filesystem $filesystem,
        ReadFactory $readFactory,
        MpLogFactory $logFactory,
        CustomerFactory $customerFactory,
        RestResponse $response,
        Data $helperData,
        MpFileFactory $mpFileFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->_filesystem = $filesystem;
        $this->_readFactory = $readFactory;
        $this->_mpLogFactory = $logFactory;
        $this->_storeManager = $storeManager;
        $this->_customerFactory = $customerFactory;
        $this->_mpFileFactory = $mpFileFactory;
        $this->_response = $response;
        $this->helperData = $helperData;
    }

    /**
     * @inheritDoc
     */
    public function mineDownloadFile($fileId, $productId, $customerId)
    {
        $file = $this->_mpFileFactory->create()->load($fileId);

        $this->downLoadFile($file, $customerId, $productId);
    }

    /**
     * @inheritDoc
     */
    public function guestDownloadFile($fileId, $productId)
    {
        $file = $this->_mpFileFactory->create()->load($fileId);

        return $this->downLoadFile($file, 0, $productId);
    }

    /**
     * @param File $file
     * @param $customerId
     * @param $productId
     *
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function downLoadFile(File $file, $customerId, $productId)
    {
        if ($customerId) {
            $customer = $this->_customerFactory->create()->load($customerId);
            $customerGroup = $customer->getGroupId();
        } else {
            $customerGroup = 0;
        }

        $fileCustomerGroup = explode(',', $file->getCustomerGroup());

        if (!$this->helperData->isApiPurchased($file->getIsBuyer(), $productId, $customerId)) {
            throw new \Magento\Framework\Webapi\Exception(__('The file is not available for you'), 101);
        }

        if ($file->getStatus() === Status::INACTIVE
            || !in_array((string)$customerGroup, $fileCustomerGroup, true)
            || (int)$file->getType() === FileType::OTHER
        ) {
            throw new \Magento\Framework\Webapi\Exception(__('The file is not available'), 101);
        }

        $mediaPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $directoryRead = $this->_readFactory->create($mediaPath);

        if ((int)$file->getType() === FileType::IN_STORE) {
            /** @var RestResponse $resultRaw */
            $resultRaw = $this->_response;
            $fileAbsolutePath = FileHelper::TEMPLATE_MEDIA_PATH . '/'
                . FileHelper::TEMPLATE_MEDIA_TYPE_FILE . '/' . $file->getFilePath();
            if (!$directoryRead->isFile($fileAbsolutePath)) {
                throw new \Magento\Framework\Webapi\Exception(__('%1 not a file', $file->getName()));
            }

            $fileNameInPath = explode('/', $file->getFilePath());
            $fileTypeInPath = explode('.', $fileNameInPath[count($fileNameInPath) - 1]);
            $fileType = explode('.', $file->getName());

            if ($fileTypeInPath[count($fileTypeInPath) - 1] !== $fileType[count($fileType) - 1]) {
                throw new \Magento\Framework\Webapi\Exception(__('Can not download file'));
            }

            $resultRaw->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-Length', strlen($directoryRead->readFile($fileAbsolutePath)))
                ->setHeader('Content-Description', 'File Transfer')
                ->setHeader('Content-Transfer-Encoding', 'binary')
                ->setHeader('Expires', '0')
                ->setHeader('Pragma', 'public')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $file->getName() . '"', true)
                ->setHeader('Content-type', $this->getContentType($file->getName()), true)
                ->setHeader('Last-Modified', date('r'), true);
            $resultRaw->setContent($directoryRead->readFile($fileAbsolutePath));
            $resultRaw->sendResponse();

            /** @var Log $file */
            $log = $this->_mpLogFactory->create();
            $logData = [
                'file_id' => $file->getId(),
                'customer_id' => $customerId,
                'product_id' => $productId,
                'file_action' => $file->getFileAction(),
                'store_id' => $this->_storeManager->getStore()->getId(),
                'customer_group' => $customerGroup
            ];

            $log->addData($logData)->save();
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getContentType($name)
    {
        $fileNameInfo = explode('.', $name);
        if (count($fileNameInfo) === 2 && array_key_exists($fileNameInfo[1], $this->mimet)) {
            return $this->mimet[$fileNameInfo[1]];
        }

        return 'application/octet-stream';
    }
}
