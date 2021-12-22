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

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CommentSearchResultsInterface
 * @api
 */
interface CommentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get comment list
     *
     * @return \Aheadworks\Ctq\Api\Data\CommentInterface[]
     */
    public function getItems();

    /**
     * Set comment list
     *
     * @param \Aheadworks\Ctq\Api\Data\CommentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
