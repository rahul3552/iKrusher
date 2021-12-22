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
namespace Aheadworks\Ctq\Model\Quote\Export\Exporter;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Export\ExporterInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\LayoutInterfaceFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Doc
 * @package Aheadworks\Ctq\Model\Quote\Export\Exporter
 */
class Doc implements ExporterInterface
{
    /**
     * @var string
     */
    protected $fileName = 'quote.doc';

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var LayoutInterfaceFactory
     */
    private $layoutFactory;

    /**
     * @param ResponseInterface $response
     * @param LayoutInterfaceFactory $layoutFactory
     */
    public function __construct(
        ResponseInterface $response,
        LayoutInterfaceFactory $layoutFactory
    ) {
        $this->response = $response;
        $this->layoutFactory = $layoutFactory;
    }
    /**
     * @inheritDoc
     */
    public function exportQuote($quote)
    {
        $this->response->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', 'application/vnd.ms-word', true)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $this->fileName . '"', true)
            ->setHeader('Last-Modified', date('r'), true)
            ->sendHeaders();

        $content = $this->getContent($quote);

        return $this->response->setBody($content);
    }

    /**
     * Retrieve content
     *
     * @param QuoteInterface $quote
     * @return string
     * @throws LocalizedException
     */
    protected function getContent($quote)
    {
        /** @var $layout LayoutInterface */
        $layout = $this->layoutFactory->create(['cacheable' => false]);
        $layout->getUpdate()->load('aw_ctq_export_quote');
        $layout->generateXml();
        $layout->generateElements();

        /** @var AbstractBlock $block */
        foreach ($layout->getAllBlocks() as $block) {
            $block->setData('quote', $quote);
        }
        
        return $layout->getOutput();
    }
}
