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
namespace Aheadworks\Ctq\Model\ResourceModel\Comment\Relation\Attachment;

use Aheadworks\Ctq\Api\Data\CommentAttachmentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Ctq\Model\ResourceModel\Comment\Relation\Attachment
 */
class SaveHandler implements ExtensionInterface
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
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if (!$entity->getAttachments()) {
            return $entity;
        }

        $entityId = (int)$entity->getId();
        $tableName = $this->resourceConnection->getTableName('aw_ctq_comment_attachment');
        $connection = $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(CommentInterface::class)->getEntityConnectionName()
        );
        $connection->delete($tableName, ['comment_id = ?' => $entityId]);

        $attachmentsToInsert = [];
        /** @var CommentAttachmentInterface $attachment */
        foreach ($entity->getAttachments() as $attachment) {
            $attachmentsToInsert[] = [
                'comment_id' => $entityId,
                'name' => $attachment->getName(),
                'file_name' => $attachment->getFileName()
            ];
        }
        if ($attachmentsToInsert) {
            $connection->insertMultiple($tableName, $attachmentsToInsert);
        }

        return $entity;
    }
}
