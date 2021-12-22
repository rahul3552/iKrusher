<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as FrameworkAbstractCollection;
use Magento\Framework\DataObject;

/**
 * Class AbstractCollection
 * @package Aheadworks\Ca\Model\ResourceModel
 */
class AbstractCollection extends FrameworkAbstractCollection
{
    /**
     * @var string[]
     */
    private $linkageTableNames = [];

    /**
     * Attach relation table data to collection items
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string|array $columnNameRelationTable
     * @param string $fieldName
     * @return void
     */
    public function attachRelationTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnNameRelationTable,
        $fieldName
    ) {
        $ids = $this->getColumnValues($columnName);
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from([$tableName . '_table' => $this->getTable($tableName)])
                ->where($tableName . '_table.' . $linkageColumnName . ' IN (?)', $ids);
            $result = $connection->fetchAll($select);

            /** @var DataObject $item */
            foreach ($this as $item) {
                $resultIds = $this->prepareItemData(
                    $item,
                    $result,
                    $columnName,
                    $linkageColumnName,
                    $columnNameRelationTable
                );
                $item->setData($fieldName, $resultIds);
            }
        }
    }

    /**
     * Prepare item data
     *
     * @param DataObject $item
     * @param array $sqlResult
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string|array $columnNameRelationTable
     * @return array
     */
    private function prepareItemData(
        $item,
        $sqlResult,
        $columnName,
        $linkageColumnName,
        $columnNameRelationTable
    ) {
        $resultIds = [];
        $id = $item->getData($columnName);
        foreach ($sqlResult as $data) {
            if ($data[$linkageColumnName] == $id) {
                if (is_array($columnNameRelationTable)) {
                    $fieldValue = [];
                    foreach ($columnNameRelationTable as $columnNameRelation) {
                        $fieldValue[$columnNameRelation] = $data[$columnNameRelation];
                    }
                    $resultIds[] = $fieldValue;
                } else {
                    $resultIds[] = $data[$columnNameRelationTable];
                }
            }
        }
        return $resultIds;
    }

    /**
     * Join to linkage table if filter is applied
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnFilter
     * @param string $fieldName
     * @return $this
     */
    public function joinLinkageTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnFilter,
        $fieldName
    ) {
        if ($this->getFilter($columnFilter)) {
            $linkageTableName = $columnFilter . '_table';
            if (in_array($linkageTableName, $this->linkageTableNames)) {
                $this->addFilterToMap($columnFilter, $columnFilter . '_table.' . $fieldName);
                return $this;
            }

            $this->linkageTableNames[] = $linkageTableName;
            $select = $this->getSelect();
            $select->joinLeft(
                [$linkageTableName => $this->getTable($tableName)],
                'main_table.' . $columnName . ' = ' . $linkageTableName . '.' . $linkageColumnName,
                []
            );

            $this->addFilterToMap($columnFilter, $columnFilter . '_table.' . $fieldName);
        }

        return $this;
    }

    /**
     * Retrieve data from table
     *
     * @param string $tableName
     * @param string $linkageColumnName
     * @param array $ids
     * @return array
     */
    protected function getDataFromTable($tableName, $linkageColumnName, $ids)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from([$tableName . '_table' => $this->getTable($tableName)])
            ->where($tableName . '_table.' . $linkageColumnName . ' IN (?)', $ids);

        return $connection->fetchAll($select);
    }
}
