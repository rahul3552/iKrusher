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
namespace Aheadworks\Ctq\Model\Comment;

use Aheadworks\Ctq\Api\Data\CommentAttachmentInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Attachment
 * @package Aheadworks\Ctq\Model\Comment
 */
class Attachment extends AbstractModel implements CommentAttachmentInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCommentId()
    {
        return $this->getData(self::COMMENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentId($commentId)
    {
        return $this->setData(self::COMMENT_ID, $commentId);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return $this->getData(self::FILE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setFileName($fileName)
    {
        return $this->setData(self::FILE_NAME, $fileName);
    }
}
