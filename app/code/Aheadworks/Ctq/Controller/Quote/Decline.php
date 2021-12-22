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
namespace Aheadworks\Ctq\Controller\Quote;

use Aheadworks\Ctq\Model\Source\Quote\Status;

/**
 * Class Decline
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Decline extends ChangeStatus
{
    /**
     * {@inheritdoc}
     */
    protected function performSave($status)
    {
        $status = Status::DECLINED_BY_BUYER;
        parent::performSave($status);
    }
}
