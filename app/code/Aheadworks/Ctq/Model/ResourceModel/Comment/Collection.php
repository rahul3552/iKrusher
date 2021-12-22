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
namespace Aheadworks\Ctq\Model\ResourceModel\Comment;

use Aheadworks\Ctq\Api\Data\CommentAttachmentInterface;
use Aheadworks\Ctq\Model\Comment;
use Aheadworks\Ctq\Model\ResourceModel\AbstractCollection;
use Aheadworks\Ctq\Model\ResourceModel\Comment as ResourceComment;
use Aheadworks\Ctq\Model\Source\Owner;

/**
 * Class Collection
 * @package Aheadworks\Ctq\Model\ResourceModel\Comment
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Comment::class, ResourceComment::class);
    }

    /**
     * Add attachment file name filter
     *
     * @param string $value
     * @return $this
     */
    public function addAttachmentFileNameFilter($value)
    {
        $this->addFilter(CommentAttachmentInterface::FILE_NAME, ['eq' => $value], 'public');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            'aw_ctq_comment_attachment',
            'id',
            'comment_id',
            ['comment_id', 'name', 'file_name'],
            'attachments'
        );
        $this
            ->attachOwnerName('admin_user', 'user_id', Owner::SELLER)
            ->attachOwnerName('customer_entity', 'entity_id', Owner::BUYER);
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable(
            'aw_ctq_comment_attachment',
            'id',
            'comment_id',
            'file_name',
            'file_name'
        );
        parent::_renderFiltersBefore();
    }
}
