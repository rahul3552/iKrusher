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
namespace Aheadworks\Ctq\Plugin\Controller;

use Aheadworks\Ctq\Model\Quote\Cleaner;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class FrontActionPlugin
 * @package Aheadworks\Ctq\Plugin\Controller
 */
class FrontActionPlugin
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
     * Clear cart if customer leave quote checkout
     *
     * @param ActionInterface $subject
     * @param RequestInterface $request
     * @return void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDispatch(
        ActionInterface $subject,
        RequestInterface $request
    ) {
        return $this->cleaner->clear($request);
    }
}
