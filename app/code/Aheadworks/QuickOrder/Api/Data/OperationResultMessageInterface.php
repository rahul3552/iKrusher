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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Api\Data;

/**
 * Interface OperationResultMessageInterface
 * @api
 */
interface OperationResultMessageInterface
{
    /**
     * #@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const TYPE = 'type';
    const TITLE = 'title';
    const TEXT = 'text';
    /**#@-*/

    /**
     * Get message type
     *
     * @return string
     */
    public function getType();

    /**
     * Get message title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get message text
     *
     * @return string
     */
    public function getText();
}
