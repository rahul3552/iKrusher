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
namespace Aheadworks\Ctq\Api\Data;

/**
 * Interface CommentAttachmentInterface
 * @api
 */
interface CommentAttachmentInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const COMMENT_ID = 'comment_id';
    const NAME = 'name';
    const FILE_NAME = 'file_name';
    /**#@-*/

    /**
     * Get comment id
     *
     * @return int
     */
    public function getCommentId();

    /**
     * Set comment id
     *
     * @param int $commentId
     * @return $this
     */
    public function setCommentId($commentId);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName();

    /**
     * Set file name
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName);
}
