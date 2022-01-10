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

namespace Mageplaza\ProductAttachments\Model\ResourceModel\Log\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mageplaza\ProductAttachments\Model\ResourceModel\Log;
use Psr\Log\LoggerInterface as Logger;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package Mageplaza\ProductAttachments\Model\ResourceModel\Log\Grid
 */
class Collection extends SearchResult
{
    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'mageplaza_productattachments_log',
        $resourceModel = Log::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addFileLabelName();
        $this->addCustomerName();
        $this->addProductName();

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        switch ($field) {
            case 'customer_name':
                return parent::addFieldToFilter(['firstname', 'lastname'], [$condition, $condition]);
            case 'name':
                $field = 'mpf.name';
                break;
            case 'label':
                $field = 'mpf.label';
                break;
            case 'product_name':
                $field = 'v1.value';
                break;
            case 'created_at':
                $field = 'main_table.created_at';
                break;
            case 'file_action':
                $field = 'main_table.file_action';
                break;
            default:
                break;
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @return $this
     */
    public function addFileLabelName()
    {
        $this->getSelect()->joinLeft(
            ['mpf' => $this->getTable('mageplaza_productattachments_file')],
            'main_table.file_id = mpf.file_id',
            ['name' => 'name', 'label' => 'label', 'group' => 'group']
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addCustomerName()
    {
        $this->getSelect()->joinLeft(
            ['ce' => $this->getTable('customer_entity')],
            'main_table.customer_id = ce.entity_id',
            ['firstname', 'lastname']
        )->columns([
            'customer_name' => new Zend_Db_Expr("CONCAT(`ce`.`firstname`,' ',`ce`.`lastname`)")
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    public function addProductName()
    {
        $eavAttributeTable     = $this->getTable('eav_attribute');
        $eavAttributeTypeTable = $this->getTable('eav_entity_type');
        $this->getSelect()->joinLeft(
            ['cpe' => $this->getTable('catalog_product_entity')],
            'main_table.product_id = cpe.entity_id',
            ['entity_id' => 'cpe.entity_id']
        )->joinLeft(
            ['v1' => $this->getTable('catalog_product_entity_varchar')],
            'cpe.entity_id = v1.entity_id AND v1.store_id = 0 AND v1.attribute_id =
                  (SELECT attribute_id
                   FROM ' . $eavAttributeTable . "
                   WHERE attribute_code = 'name'
                     AND entity_type_id =
                       (SELECT entity_type_id
                        FROM " . $eavAttributeTypeTable . "
                        WHERE entity_type_code = 'catalog_product'))",
            ['product_name' => 'v1.value']
        );

        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     *
     * @return SearchResult
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field === 'created_at') {
            $field = 'main_table.created_at';
        }

        if ($field === 'group') {
            $field = 'mpf.group';
        }

        return parent::setOrder($field, $direction);
    }
}
