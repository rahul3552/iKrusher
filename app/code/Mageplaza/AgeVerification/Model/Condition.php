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
 * @package     Mageplaza_AgeVerification
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AgeVerification\Model;

use Magento\Backend\Model\Session;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
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
use Magento\Rule\Model\Action\Collection;
use Mageplaza\AgeVerification\Helper\Data as HelperData;

/**
 * Class Condition
 * @package Mageplaza\AgeVerification\Model
 */
class Condition extends AbstractModel
{
    /**
     * Store matched product Ids
     *
     * @var array
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
     * Store matched product Ids with rule id
     *
     * @var array
     */
    protected $dataProductIds;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Iterator
     */
    protected $resourceIterator;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Condition constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param RequestInterface $request
     * @param Session $backendSession
     * @param ProductFactory $productFactory
     * @param CollectionFactory $productCollectionFactory
     * @param HelperData $helperData
     * @param Iterator $resourceIterator
     * @param AbstractDb|null $resourceCollection
     * @param AbstractResource|null $resource
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        RequestInterface $request,
        Session $backendSession,
        ProductFactory $productFactory,
        CollectionFactory $productCollectionFactory,
        HelperData $helperData,
        Iterator $resourceIterator,
        AbstractDb $resourceCollection = null,
        AbstractResource $resource = null
    ) {
        $this->_request = $request;
        $this->_backendSession = $backendSession;
        $this->productFactory = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_helperData = $helperData;
        $this->resourceIterator = $resourceIterator;

        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection);
    }

    /**
     * @param $condition
     *
     * @return array|null
     */
    public function getMatchingProductIds($condition)
    {
        if ($this->_productIds === null) {
            $this->_productIds = [];

            /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH)
                ->addAttributeToFilter(
                    'status',
                    Status::STATUS_ENABLED
                );

            $this->setConditionsSerialized($condition);
            $this->getConditions()->collectValidatedAttributes($productCollection);

            $this->resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product' => $this->productFactory->create(),
                ]
            );
        }

        return $this->_productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param $args
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }

    /**
     * @return Collection|\Magento\Rule\Model\Condition\Combine|mixed
     */
    public function getConditionsInstance()
    {
        return $this->getActionsInstance();
    }

    /**
     * @return Collection|mixed
     */
    public function getActionsInstance()
    {
        return ObjectManager::getInstance()->create(Combine::class);
    }
}
