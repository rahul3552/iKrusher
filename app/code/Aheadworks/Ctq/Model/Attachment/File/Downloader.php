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
namespace Aheadworks\Ctq\Model\Attachment\File;

use Aheadworks\Ctq\Api\Data\CommentAttachmentInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Downloader
 * @package Aheadworks\Ctq\Model\Attachment\File
 */
class Downloader
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Info
     */
    private $info;

    /**
     * @param ResponseInterface $response
     * @param Info $info
     */
    public function __construct(
        ResponseInterface $response,
        Info $info
    ) {
        $this->response = $response;
        $this->info = $info;
    }

    /**
     * Download file
     *
     * @param CommentAttachmentInterface $attachment
     * @return ResponseInterface
     * @throws LocalizedException
     */
    public function download($attachment)
    {
        $fileName = $attachment->getFileName();
        $file = $this->info->getFilePath($fileName);
        if (!$this->info->getMediaDirectory()->isFile($file)) {
            throw new LocalizedException(__('File not found.'));
        }
        $contentLength = $this->info->getStat($fileName)['size'];

        $this->response->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', 'application/octet-stream', true)
            ->setHeader('Content-Length', $contentLength, true)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $attachment->getName() . '"', true)
            ->setHeader('Last-Modified', date('r'), true)
            ->sendHeaders();

        $stream = $this->info->getMediaDirectory()->openFile($file, 'r');
        $content = '';
        while (!$stream->eof()) {
            $content .= $stream->read(1024);
        }
        $stream->close();

        return $this->response->setBody($content);
    }
}
