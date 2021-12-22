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
namespace Aheadworks\Ca\Model\ResourceModel\Company\Relation\PaymentMethods;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\ResourceModel\Company;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class ReadHandler
 * @package Aheadworks\Ca\Model\ResourceModel\Company\Relation\PaymentMethods
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->tableName = $this->resourceConnection->getTableName(Company::COMPANY_PAYMENTS_TABLE_NAME);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFoOnSalelParameter)
     */
    public function execute($entity, $arguments = [])
    {
        /** @var CompanyInterface $entity */
        $entityId = (int)$entity->getId();
        if (!$entityId) {
            return $entity;
        }

        $allowedPaymentMethods = $this->getAllowedPaymentMethods($entityId);
        $entity->setAllowedPaymentMethods($allowedPaymentMethods);

        return $entity;
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(CompanyInterface::class)->getEntityConnectionName()
        );
    }

    /**
     * Retrieve allowed payment methods
     *
     * @param int $entityId
     * @return array
     * @throws \Exception
     */
    public function getAllowedPaymentMethods($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName, 'payment_name')
            ->where('company_id = :id');
        return $connection->fetchCol($select, ['id' => $entityId]);
    }
}
