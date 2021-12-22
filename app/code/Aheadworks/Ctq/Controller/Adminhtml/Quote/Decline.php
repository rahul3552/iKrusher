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
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Aheadworks\Ctq\Model\Source\Quote\Status;

/**
 * Class Decline
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class Decline extends Save
{
    /**
     * {@inheritdoc}
     */
    protected function prepareQuote($data)
    {
        $quoteObject = parent::prepareQuote($data);
        $quoteObject->setStatus(Status::DECLINED_BY_SELLER);

        return $quoteObject;
    }
}
