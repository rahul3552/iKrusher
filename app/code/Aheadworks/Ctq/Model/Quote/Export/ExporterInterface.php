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
namespace Aheadworks\Ctq\Model\Quote\Export;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface ExporterInterface
 * @package Aheadworks\Ctq\Model\Quote\Export
 */
interface ExporterInterface
{
    /**
     * Export quote
     *
     * @param QuoteInterface $quote
     * @return ResponseInterface
     * @throws LocalizedException
     */
    public function exportQuote($quote);
}
