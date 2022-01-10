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

namespace Mageplaza\ProductAttachments\Model;

use Magento\Backend\Model\Session;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogRule\Model\Rule\Condition\Combine;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Model\ResourceModel\File as FileResource;

/**
 * Class File
 * @package Mageplaza\ProductAttachments\Model
 */
class File extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_productattachments_file';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_productattachments_file';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_productattachments_file';

    /**
     * @var string
     */
    protected $_idFieldName = 'file_id';

    /**
     * @var
     */
    protected $_productIds;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Session
     */
    protected $_backendSession;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Iterator
     */
    protected $_resourceIterator;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * File constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param RequestInterface $request
     * @param Session $backendSession
     * @param ProductFactory $productFactory
     * @param Iterator $iterator
     * @param Data $helperData
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        RequestInterface $request,
        Session $backendSession,
        ProductFactory $productFactory,
        Iterator $iterator,
        Data $helperData,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);

        $this->_request = $request;
        $this->_backendSession = $backendSession;
        $this->_productFactory = $productFactory;
        $this->_resourceIterator = $iterator;
        $this->_helperData = $helperData;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(FileResource::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->getActionsInstance();
    }

    /**
     * @return Combine|\Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return ObjectManager::getInstance()->create(Combine::class);
    }

    /**
     * Get matched catalog rule product ids
     *
     * @return array|null
     */
    public function getMatchingProductIds()
    {
        if ($this->_productIds === null) {
            $data = $this->_request->getPost('rule');
            $storeIds = $this->_request->getPost('file')
                ? $this->_request->getPost('file')['store_ids'] : $this->getStoreIds();

            /** Fix filter grid error */
            if ($data) {
                $this->_backendSession->setProductAttachmentsData(['rule' => $data, 'store_ids' => $storeIds]);
            } else {
                $productAttachmentsData = $this->_backendSession->getProductAttachmentsData();
                $data = $productAttachmentsData['rule'];
                $storeIds = $productAttachmentsData['store_ids'];
            }

            if (!$data) {
                $data = [];
            }
            $this->_productIds = [];
            $this->setCollectedAttributes([]);

            /** @var $productCollection Collection */
            $productCollection = $this->_productFactory->create()->getCollection();
            $productCollection->addAttributeToSelect('*')->addFieldToSelect('*')->addAttributeToFilter('status', 1);

            $this->loadPost($data);
            $this->setConditionsSerialized($this->_helperData->serialize($this->getConditions()->asArray()));
            $this->getConditions()->collectValidatedAttributes($productCollection);
            $this->_resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProductConditions']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product' => $this->_productFactory->create(),
                    'store_ids' => $storeIds
                ]
            );
        }

        return $this->_productIds;
    }

    /**
     * Callback function for product matching (conditions)
     *
     * @param array $args
     *
     * @return void
     */
    public function callbackValidateProductConditions($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        $storeIds = $args['store_ids'];

        foreach ($storeIds as $storeId) {
            $product->setStoreId($storeId);
        }
        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }
}
