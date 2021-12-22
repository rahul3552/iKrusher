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
namespace Aheadworks\Ctq\Model\Quote;

use Magento\Framework\UrlInterface;
use Magento\Framework\Url as FrontendUrl;
use Magento\Backend\Model\Url as BackendUrl;

/**
 * Class Url
 * @package Aheadworks\Ctq\Model\Quote
 */
class Url
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var FrontendUrl
     */
    private $urlBuilderFrontend;

    /**
     * @var BackendUrl
     */
    private $urlBuilderBackend;

    /**
     * @param UrlInterface $urlBuilder
     * @param FrontendUrl $urlBuilderFrontend
     * @param BackendUrl $urlBuilderBackend
     */
    public function __construct(
        UrlInterface $urlBuilder,
        FrontendUrl $urlBuilderFrontend,
        BackendUrl $urlBuilderBackend
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->urlBuilderFrontend = $urlBuilderFrontend;
        $this->urlBuilderBackend = $urlBuilderBackend;
    }

    /**
     * Retrieve quote url
     *
     * @param int $quoteId
     * @return string
     */
    public function getQuoteUrl($quoteId)
    {
        return $this->urlBuilder->getUrl('aw_ctq/quote/edit', ['quote_id' => $quoteId]);
    }

    /**
     * Retrieve frontend quote url
     *
     * @param int $quoteId
     * @return string
     */
    public function getFrontendQuoteUrl($quoteId)
    {
        return $this->urlBuilderFrontend->getUrl('aw_ctq/quote/edit', ['quote_id' => $quoteId]);
    }

    /**
     * Retrieve admin quote url
     *
     * @param int $quoteId
     * @return string
     */
    public function getAdminQuoteUrl($quoteId)
    {
        return $this->urlBuilderBackend->getUrl('aw_ctq/quote/edit', ['id' => $quoteId]);
    }

    /**
     * Retrieve downloadable url
     *
     * @param string $attachmentFileName
     * @param int $quoteId
     * @param int $commentId
     * @return string
     */
    public function getDownloadUrl($attachmentFileName, $quoteId, $commentId)
    {
        $params = [
            'file' => $attachmentFileName,
            'quote_id' => $quoteId,
            'comment_id' => $commentId
        ];

        return $this->urlBuilder->getUrl('aw_ctq/quote/download', $params);
    }

    /**
     * Retrieve frontend downloadable url
     *
     * @param string $attachmentFileName
     * @param int $quoteId
     * @param int $commentId
     * @return string
     */
    public function getFrontendDownloadUrl($attachmentFileName, $quoteId, $commentId)
    {
        $params = [
            'file' => $attachmentFileName,
            'quote_id' => $quoteId,
            'comment_id' => $commentId
        ];

        return $this->urlBuilderFrontend->getUrl('aw_ctq/quote/download', $params);
    }

    /**
     * Retrieve admin downloadable url
     *
     * @param string $attachmentFileName
     * @param int $quoteId
     * @param int $commentId
     * @return string
     */
    public function getAdminDownloadUrl($attachmentFileName, $quoteId, $commentId)
    {
        $params = [
            'file' => $attachmentFileName,
            'quote_id' => $quoteId,
            'comment_id' => $commentId
        ];

        return $this->urlBuilderBackend->getUrl('aw_ctq/quote/download', $params);
    }

    /**
     * Retrieve add comment url
     *
     * @return string
     */
    public function getAddCommentUrl()
    {
        return $this->urlBuilder->getUrl('aw_ctq/quote/addComment');
    }
}
