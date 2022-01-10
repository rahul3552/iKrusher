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

use Closure;
use Exception;
use Magento\Backend\Block\Template;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as ItemCollection;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Mageplaza\ProductAttachments\Helper\File as HelperFile;
use Mageplaza\ProductAttachments\Mail\Template\TransportBuilder;
use Mageplaza\ProductAttachments\Model\Config\Source\System\ShowOn;
use Mageplaza\ProductAttachments\Model\File as ModelFile;
use Mageplaza\ProductAttachments\Model\ResourceModel\File as ResourceModelFile;
use Mageplaza\ProductAttachments\Model\FileFactory;
use Mageplaza\ProductAttachments\Model\ResourceModel\File\Collection;
use Mageplaza\ProductAttachments\Model\ResourceModel\File\CollectionFactory as FileCollection;
use Zend\Mail\Message;
use Zend\Mime\Message as MineMessage;
use Zend\Mime\Part;
use Zend_Mime;

/**
 * Class Data
 * @package Mageplaza\ProductAttachments\Helper
 */
class Data extends CoreHelper
{
    const CONFIG_MODULE_PATH                  = 'productattachments';
    const ATTACHMENTS_LOCATION_ATTRIBUTE_CODE = 'mp_attachments_location';
    const MAX_SIZE_FILE_UPLOAD                = 25;

    /**
     * @var File
     */
    protected $_helperFile;

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var FileCollection
     */
    protected $_fileCollection;

    /**
     * @var Url
     */
    protected $_customerUrl;

    /**
     * @var ShowOn
     */
    protected $_showOn;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ReadFactory
     */
    protected $readFactory;

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    private $fileSizeMax = 0;

    /**
     * @var ItemCollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Url $customerUrl
     * @param File $helperFile
     * @param FileFactory $fileFactory
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param FileCollection $fileCollection
     * @param ShowOn $showOn
     * @param ProductFactory $productFactory
     * @param TransportBuilder $transportBuilder
     * @param Filesystem $filesystem
     * @param ReadFactory $readFactory
     * @param SessionManagerInterface $sessionManager
     * @param ItemCollectionFactory $itemCollectionFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Url $customerUrl,
        HelperFile $helperFile,
        FileFactory $fileFactory,
        OrderCollectionFactory $orderCollectionFactory,
        FileCollection $fileCollection,
        ShowOn $showOn,
        ProductFactory $productFactory,
        TransportBuilder $transportBuilder,
        Filesystem $filesystem,
        ReadFactory $readFactory,
        SessionManagerInterface $sessionManager,
        ItemCollectionFactory $itemCollectionFactory
    ) {
        $this->_customerUrl           = $customerUrl;
        $this->_helperFile            = $helperFile;
        $this->_fileFactory           = $fileFactory;
        $this->_fileCollection        = $fileCollection;
        $this->_showOn                = $showOn;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->productFactory         = $productFactory;
        $this->transportBuilder       = $transportBuilder;
        $this->filesystem             = $filesystem;
        $this->readFactory            = $readFactory;
        $this->sessionManager         = $sessionManager;
        $this->itemCollectionFactory  = $itemCollectionFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param string $file
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageUrl($file)
    {
        return $this->_helperFile->getBaseMediaUrl() . '/' . $this->_helperFile->getMediaPath(
            $file,
            HelperFile::TEMPLATE_MEDIA_TYPE_ICON
        );
    }

    /**
     * @param string $file
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getFileUrl($file)
    {
        return $this->_helperFile->getBaseMediaUrl() . '/' . $this->_helperFile->getMediaPath(
            $file,
            HelperFile::TEMPLATE_MEDIA_TYPE_FILE
        );
    }

    /**
     * Get file list in each product ( in admin )
     *
     * @param int $productId
     *
     * @return AbstractCollection
     */
    public function getFilesByProductId($productId)
    {
        $fileCollection = $this->_fileFactory->create()->getCollection();
        $fileCollection->join(
            ['product' => $fileCollection->getTable('mageplaza_productattachments_file_product')],
            'main_table.file_id=product.file_id AND product.entity_id=' . $productId
        )->setOrder('position', 'asc');

        return $fileCollection;
    }

    /**
     * @param Collection $collection
     * @param null $storeId
     *
     * @return mixed
     */
    public function addStoreFilter($collection, $storeId = null)
    {
        if ($storeId === null) {
            try {
                $storeId = $this->storeManager->getStore()->getId();
            } catch (NoSuchEntityException $e) {
                $this->_logger->critical($e);
            }
        }

        $collection->addFieldToFilter('store_ids', [
            ['finset' => Store::DEFAULT_STORE_ID],
            ['finset' => $storeId]
        ]);

        return $collection;
    }

    /**
     * Get default icon image url
     *
     * @return mixed
     */
    public function getDefaultIconUrl()
    {
        /** @var Template $blockTemplate */
        $blockTemplate = $this->objectManager->create(Template::class);

        return $blockTemplate->getViewFileUrl(
            'Mageplaza_ProductAttachments::media/icons/file-default.png',
            ['area' => Area::AREA_FRONTEND]
        );
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getIconUrl()
    {
        return $this->_helperFile->getBaseMediaUrl() . '/'
            . $this->_helperFile->getBaseMediaPath(HelperFile::TEMPLATE_MEDIA_TYPE_ICON);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDefaultValueConfig($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig('default_value' . $code, $storeId);
    }

    /**
     * @return array
     */
    public function getShowOnLocation()
    {
        $locations = $this->_showOn->toOptionArray();
        array_shift($locations);

        return $locations;
    }

    /**
     * @param int $size
     *
     * @return string
     */
    public function fileSizeFormat($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $power = $size > 0 ? (int) floor(log($size, 1024)) : 0;

        return number_format($size / (1024 ** $power), 2) . ' ' . $units[$power];
    }

    /**
     * Get file collection
     *
     * @param null $storeId
     *
     * @return Collection
     */
    public function getFileCollection($storeId = null)
    {
        /** @var Collection $collection */
        $collection = $this->_fileCollection->create()
            ->addFieldToFilter('status', 1)
            ->setOrder('priority', 'asc');
        $this->addStoreFilter($collection, $storeId);

        return $collection;
    }

    /**
     * @param string $group
     * @param bool $isFrontend
     * @param array $groupValue
     *
     * @return Collection
     */
    public function getFileByRule($group = '', $isFrontend = false, $groupValue = [])
    {
        $collection = $this->getFileCollection()->addFieldToFilter('is_grid', 1);

        if ($group === 'others') {
            $condition = [['null' => true], ['eq' => '']];
            if (!empty($groupValue)) {
                array_push($condition, ['nin' => $groupValue]);
            }

            $groups  = $this->getGroups();
            $options = $groups ? Data::jsonDecode($groups) : [];

            if (isset($options['option']['value']) && !empty($options['option']['value'])) {
                $collection->addFieldToFilter('group', $condition);
            }
        } elseif ($group && $isFrontend) {
            $collection->addFieldToFilter('group', $group);
        }

        return $collection;
    }

    /**
     * Get file list in each product ( in frontend )
     *
     * @param int $productId
     * @param string $group
     * @param bool $isFrontend
     * @param array $groupValue
     *
     * @return Collection
     */
    public function getFileByProduct($productId, $group = '', $isFrontend = false, $groupValue = [])
    {
        $fileCollection = $this->getFileCollection();
        $fileCollection->join(
            ['product' => $fileCollection->getTable('mageplaza_productattachments_file_product')],
            'main_table.file_id=product.file_id AND product.entity_id=' . $productId
        );

        if ($group === 'others') {
            $condition = [['null' => true], ['eq' => '']];
            if (!empty($groupValue)) {
                array_push($condition, ['nin' => $groupValue]);
            }

            $groups  = $this->getGroups();
            $options = $groups ? Data::jsonDecode($groups) : [];

            if (isset($options['option']['value']) && !empty($options['option']['value'])) {
                $fileCollection->addFieldToFilter('main_table.group', $condition);
            }
        } elseif ($group && $isFrontend) {
            $fileCollection->addFieldToFilter('main_table.group', $group);
        }

        return $fileCollection;
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->_customerUrl->getLoginUrl();
    }

    /**
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->_customerUrl->getRegisterUrl();
    }

    /**
     * @param ResourceModelFile $resource
     * @param ModelFile $object
     * @param string $originName
     *
     * @return string
     * @throws LocalizedException
     */
    public function generateFileName($resource, $object, $originName)
    {
        $attempt = -1;
        do {
            if ($attempt++ >= 30) {
                throw new LocalizedException(__('Unable to generate file name. Please check the setting and try again.'));
            }
            $fileName = $originName;
            if ($fileName) {
                $withoutExt = pathinfo($fileName, PATHINFO_FILENAME);
                $ext        = pathinfo($fileName, PATHINFO_EXTENSION);
                $fileName   = (!empty($ext) && $ext)
                    ? $withoutExt . ($attempt ?: '') . '.' . $ext : $withoutExt . ($attempt ?: '');
            }
        } while ($this->checkFileName($resource, $object, $fileName));

        return $fileName;
    }

    /**
     * @param ResourceModelFile $resource
     * @param ModelFile $object
     * @param string $fileName
     *
     * @return bool
     * @throws LocalizedException
     */
    public function checkFileName($resource, $object, $fileName)
    {
        if (empty($fileName)) {
            return true;
        }

        $adapter = $resource->getConnection();
        $select  = $adapter->select()
            ->from($resource->getMainTable(), '*')
            ->where('name = :name');

        $binds = ['name' => (string) $fileName];

        if ($id = $object->getId()) {
            $select->where($resource->getIdFieldName() . ' != :object_id');
            $binds['object_id'] = (int) $id;
        }

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @param string $fileIsBuyer
     * @param string $orderStatuses
     * @param int $productId
     *
     * @return bool
     */
    public function isPurchased($fileIsBuyer, $orderStatuses, $productId)
    {
        if (!$fileIsBuyer) {
            return true;
        }

        // fix bug get Customer with cache
        $customerSession = $this->objectManager->create(Session::class);

        if (!$customerSession->isLoggedIn()) {
            return false;
        }

        if ($fileIsBuyer && empty($orderStatuses)) {
            return false;
        }

        $customerId = $customerSession->getCustomerId();
        $collection = $this->getPurchasedOrderWithProduct($customerId, $productId, $orderStatuses, $this->getStoreId());

        if ($collection->getSize()) {
            return true;
        }

        return false;
    }

    /**
     * @param int $customerId
     * @param int $productId
     * @param string $orderStatus
     * @param int $storeId
     *
     * @return ItemCollection
     */
    public function getPurchasedOrderWithProduct($customerId, $productId, $orderStatus, $storeId)
    {
        $resource   = $this->itemCollectionFactory->create();

        $resource->getSelect()->joinInner(
            ['so' => $resource->getTable('sales_order')],
            'main_table.order_id = so.entity_id',
            ['so.customer_id', 'so.status']
        )->where('so.customer_id = ?', $customerId)
        ->where('main_table.product_id = ?', $productId)
        ->where('so.status IN (?)', explode(',', $orderStatus))
        ->where('main_table.store_id = ?', $storeId);

        return $resource;
    }

    /**
     * @param bool $fileIsBuyer
     * @param int $productId
     * @param int $customerId
     *
     * @return bool
     */
    public function isApiPurchased($fileIsBuyer, $productId, $customerId)
    {
        if (!$fileIsBuyer) {
            return true;
        }

        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', 'complete');

        $productIds = [];
        /** @var Order $order */
        foreach ($orderCollection as $order) {
            foreach ($order->getAllVisibleItems() as $item) {
                $productIds[] = $item->getProductId();
            }
        }
        $productIdList = array_unique($productIds);

        return in_array($productId, $productIdList);
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (Exception $e) {
            $storeId = null;
        }

        return $storeId;
    }

    /**
     * @return mixed
     */
    public function getDisplayFileSize()
    {
        return $this->getConfigGeneral('display_file_size', $this->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getLimitFileSizeUpload()
    {
        return $this->getConfigGeneral('file_size_limit', $this->getStoreId());
    }

    /**
     * @return mixed
     */
    public function isAllowAttachedFiles()
    {
        return $this->getConfigGeneral('allow_attached_files', $this->getStoreId());
    }

    /**
     * @return mixed
     */
    public function isAllowViewOrDownload()
    {
        return $this->getDefaultValueConfig('order_status', $this->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->getConfigGeneral('groups');
    }

    /**
     * @return Closure
     */
    public function sortByPosition()
    {
        return function ($a, $b) {
            $diff = strcmp($a['position'], $b['position']);
            if ($diff !== 0) {
                return $diff;
            }

            return false;
        };
    }

    /**
     * @param Order $order
     *
     * @throws LocalizedException
     */
    public function sendAttachedFiles($order)
    {
        if ($order && $order->getId() && $this->isAllowAttachedFiles()) {
            $storeId       = $order->getStoreId();
            $emailTemplate = 'mp_product_attachments_attached__email_template';
            $sendFrom      = 'sales';
            $sendTo        = $order->getCustomerEmail();

            $attached      = [];
            $fileInRule    = [];
            /** @var Item $item */
            foreach ($order->getAllVisibleItems() as $item) {
                $product        = $this->productFactory->create()->load($item->getProductId());
                $attachedFiles  = $this->getFileByProduct($item->getProductId());
                $fileCollection = $this->getFileByRule();
                foreach ($fileCollection as $file) {
                    $customerGroups = explode(',', $file->getCustomerGroup());
                    if ($file->getConditions()->validate($product)
                        && in_array((string) $order->getCustomerGroupId(), $customerGroups, true)) {
                        $fileInRule[] = $file;
                    }
                }

                if (!empty($fileInRule)) {
                    $attached = $this->prepareDataFile($fileInRule, $attached);
                }

                if (!empty($attachedFiles)) {
                    $attached = $this->prepareDataFile($attachedFiles, $attached);
                }
            }

            if (!empty($attached)) {
                $this->transportBuilder
                    ->setTemplateIdentifier($emailTemplate)
                    ->setTemplateOptions([
                        'area'  => Area::AREA_FRONTEND,
                        'store' => $storeId,
                    ])
                    ->setTemplateVars([
                        'order_id'     => $order->getIncrementId(),
                        'order_url'    => '<a href="' . $this->getOrderUrl($order->getId()) . '">'. $order->getIncrementId() .'</a>',
                        'emailSubject' => __('Information attached to the order %1', $order->getIncrementId()),
                    ])
                    ->setFrom($sendFrom)
                    ->addTo($sendTo);

                $this->attachEmail($attached);
            }
        }
    }

    /**
     * @param int $orderId
     *
     * @return string
     */
    public function getOrderUrl($orderId)
    {
        return $this->_getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * File Extensions Exclude https://support.google.com/mail/answer/6590?hl=en#zippy=%2Cmessages-that-have-attachments
     *
     * @param array|Collection $files
     * @param array $attached
     *
     * @return array
     * @throws FileSystemException
     */
    public function prepareDataFile($files, $attached)
    {
        $mediaPath             = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $directoryRead         = $this->readFactory->create($mediaPath);
        $maxFileSizeUpload     = (float) $this->getLimitFileSizeUpload() ? : Data::MAX_SIZE_FILE_UPLOAD;
        $fileExtensionsExclude = explode(',', $this->getDefaultValueConfig('file_extensions_exclude'));

        foreach ($files as $file) {
            $fileSize           = (float) number_format($file->getSize() / (1024 ** 2), 2);
            $fileAbsolutePath   = HelperFile::TEMPLATE_MEDIA_PATH . '/'
                . HelperFile::TEMPLATE_MEDIA_TYPE_FILE . '/' . $file->getFilePath();
            $content            = $directoryRead->readFile($fileAbsolutePath);
            $ext                = pathinfo($fileAbsolutePath, PATHINFO_EXTENSION);
            if ($fileSize > Data::MAX_SIZE_FILE_UPLOAD || in_array($ext, $fileExtensionsExclude, true)) {
                continue;
            }
            $fileSizeEmail = $this->fileSizeMax + $fileSize;
            if ($content && $fileSize <= $maxFileSizeUpload && $fileSizeEmail <= Data::MAX_SIZE_FILE_UPLOAD) {
                $this->fileSizeMax += $fileSize;
                $attached[]         = $this->transportBuilder->addAttachment(
                    $content,
                    $mimeType = Zend_Mime::TYPE_OCTETSTREAM,
                    $disposition = Zend_Mime::DISPOSITION_ATTACHMENT,
                    $encoding = Zend_Mime::ENCODING_BASE64,
                    $file->getName()
                );
            }
        }

        return $attached;
    }

    /**
     * @param array $attached
     *
     * @throws LocalizedException
     * @throws MailException
     */
    public function attachEmail($attached)
    {
        if ($this->versionCompare('2.3.3')) {
            $this->sessionManager->start();
            $this->sessionManager->setMpAttached($attached);
            $transport = $this->transportBuilder->getTransport();
        } else {
            $transport            = $this->transportBuilder->getTransport();
            $html                 = $transport->getMessage();
            $message              = Message::fromString($html->getRawMessage());
            $bodyMessage          = new Part($message->getBody());
            $bodyMessage->type    = 'text/html';
            $bodyMessage->charset = 'utf-8';
            $bodyPart             = new MineMessage();

            $parts = [$bodyMessage];
            if (!empty($attached)) {
                foreach ($attached as $attache) {
                    array_push($parts, $attache);
                }

            }
            $bodyPart->setParts($parts);
            $transport->getMessage()->setBody($bodyPart);
        }

        $transport->sendMessage();

        $this->fileSizeMax = 0;
    }
}
