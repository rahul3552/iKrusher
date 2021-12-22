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
namespace Aheadworks\Ctq\Block\QuoteList;

use Aheadworks\Ctq\Model\Request\Checker;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Class DiscountProcessor
 * @package Aheadworks\Ctq\Block\QuoteList
 */
class DiscountProcessor implements LayoutProcessorInterface
{
    /**
     * @var Checker
     */
    private $checker;

    /**
     * @param Checker $checker
     */
    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    /**
     * @inheritDoc
     */
    public function process($jsLayout)
    {
        unset($jsLayout['components']['block-totals']['children']['discount']);

        return $jsLayout;
    }
}
