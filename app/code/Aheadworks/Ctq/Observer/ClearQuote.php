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
namespace Aheadworks\Ctq\Observer;

use Aheadworks\Ctq\Model\Quote\Cleaner;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Class ClearQuote
 * @package Aheadworks\Ctq\Observer
 */
class ClearQuote implements ObserverInterface
{
    /**
     * @var Cleaner
     */
    private $cleaner;

    /**
     * @param Cleaner $cleaner
     */
    public function __construct(
        Cleaner $cleaner
    ) {
        $this->cleaner = $cleaner;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $this->cleaner->clear($request);
    }
}
