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
namespace Aheadworks\Ctq\ViewModel\Customer;

use Aheadworks\Ctq\Model\Config;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class FileUploader
 * @package Aheadworks\Ctq\ViewModel\Customer
 */
class FileUploader implements ArgumentInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param ArrayManager $arrayManager
     * @param Config $config
     * @param UrlInterface $url
     */
    public function __construct(
        ArrayManager $arrayManager,
        Config $config,
        UrlInterface $url
    ) {
        $this->arrayManager = $arrayManager;
        $this->config = $config;
        $this->url = $url;
    }

    /**
     * Retrieve serialized JS layout configuration ready to use in template
     *
     * @param array $jsLayout
     * @return array
     */
    public function prepareJsLayout($jsLayout)
    {
        $fileUploaderPath = $this->arrayManager->findPath('awCtqFileUploader', $jsLayout);
        if ($fileUploaderPath) {
            $fileUploaderLayout = $this->arrayManager->get($fileUploaderPath, $jsLayout);
            $fileUploaderLayout['uploaderConfig'] = [
                'url' => $this->getFileUploadUrl()
            ];
            $fileUploaderLayout = array_merge(
                $fileUploaderLayout,
                [
                    'maxFileSize' => $this->config->getMaxUploadFileSize(),
                    'allowedExtensions' => $this->config->getAllowFileExtensions(),
                    'notice' => $this->getNotice()
                ]
            );
            $jsLayout = $this->arrayManager->merge($fileUploaderPath, $jsLayout, $fileUploaderLayout);
        }
        return $jsLayout;
    }

    /**
     * Retrieve file upload url
     *
     * @return string
     */
    private function getFileUploadUrl()
    {
        return $this->url->getUrl('aw_ctq/quote/upload', ['_secure' => true]);
    }

    /**
     * Retrieve notice
     *
     * @return \Magento\Framework\Phrase|string
     */
    private function getNotice()
    {
        if (!empty($this->config->getAllowFileExtensions())) {
            $fileTypes = implode(', ', $this->config->getAllowFileExtensions());
            return __('The following file types are allowed: %1', $fileTypes);
        }

        return '';
    }
}
