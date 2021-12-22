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

/**
 * Class Composite
 * @package Aheadworks\Ctq\Model\Quote\Export
 */
class Composite
{
    /**
     * @var array
     */
    private $exporters = [];

    /**
     * @param array $exporters
     */
    public function __construct(
        array $exporters = []
    ) {
        $this->exporters = $exporters;
    }

    /**
     * Export quote
     *
     * @param QuoteInterface $quote
     * @param string $type
     * @return ResponseInterface
     * @throws \Exception
     */
    public function exportQuote($quote, $type)
    {
        $exporter = isset($this->exporters[$type]) ? $this->exporters[$type] : null;

        if ($exporter instanceof ExporterInterface) {
            return $exporter->exportQuote($quote);
        }
        
        throw new \Exception(sprintf('Unknown file type: %s requested', $type));
    }
}
