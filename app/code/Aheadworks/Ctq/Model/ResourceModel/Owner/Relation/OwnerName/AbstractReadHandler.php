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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\ResourceModel\Owner\Relation\OwnerName;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Model\Source\Owner;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class AbstractReadHandler
 * @package Aheadworks\Ctq\Model\ResourceModel\Owner\Relation\OwnerName
 */
abstract class AbstractReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ((int)$entity->getId()) {
            $connection = $this->getConnection();
            $tableName = $entity->getOwnerType() == Owner::SELLER ? 'admin_user' : 'customer_entity';
            $idFieldName = $entity->getOwnerType() == Owner::SELLER ? 'user_id' : 'entity_id';

            $select = $connection->select()
                ->from($this->resourceConnection->getTableName($tableName), ['firstname', 'lastname'])
                ->where($idFieldName. ' = ?', $entity->getOwnerId());
            if ($result = $connection->fetchRow($select)) {
                $entity->setOwnerName($result['firstname'] . ' ' . $result['lastname']);
            }
        }
        return $entity;
    }

    /**
     * Retrieve connection
     *
     * @return AdapterInterface
     */
    abstract protected function getConnection();
}
