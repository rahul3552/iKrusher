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

namespace Mageplaza\CustomForm\Model\ResourceModel\Responses\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mageplaza\CustomForm\Model\ResourceModel\Responses as ResponsesResource;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 * @package Mageplaza\CustomForm\Model\ResourceModel\Responses\Grid
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
        $mainTable = 'mageplaza_custom_form_responses',
        $resourceModel = ResponsesResource::class
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * @return $this|SearchResult|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['mp_cf_form' => $this->getTable('mageplaza_custom_form_form')],
            'main_table.form_id = mp_cf_form.id',
            ['form_name' => 'mp_cf_form.name']
        )->joinLeft(
            ['ce' => $this->getTable('customer_entity')],
            'main_table.customer_id = ce.entity_id',
            [
                'customer_email' => 'ce.email',
                'firstname' => 'IF(main_table.customer_id = 0, "Guest" ,ce.firstname)',
                'lastname',
                'middlename',
                'prefix',
                'suffix'
            ]
        );

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return $this|SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'firstname' || $field === 'form_name') {
            $this->getSelect()->having("{$field} like ?", $condition['like']);

            return $this;
        }
        if ($field === 'store_ids' || $field === 'id' || $field === 'created_at') {
            $field = "main_table.{$field}";
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @return Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $select = clone $this->getSelect();

        $select->reset(Select::ORDER);

        return $this->getConnection()->select()->from($select, 'COUNT(*)');
    }
}
