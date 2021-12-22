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
namespace Aheadworks\Ctq\Model\ResourceModel;

use Aheadworks\Ctq\Api\Data\HistoryInterface;

/**
 * Class History
 * @package Aheadworks\Ctq\Model\ResourceModel
 */
class History extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_ctq_history';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, HistoryInterface::ID);
    }
}
