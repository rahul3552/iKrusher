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

use Aheadworks\Ctq\Model\Config;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action as BackendAction;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Aheadworks\Ctq\Model\Attachment\File\Uploader as FileUploader;
use Aheadworks\Ctq\Controller\Quote\Upload as FrontendQuoteUpload;

/**
 * Class Upload
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class Upload extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param FileUploader $fileUploader
     * @param Config $config
     */
    public function __construct(
        Context $context,
        FileUploader $fileUploader,
        Config $config
    ) {
        parent::__construct($context);
        $this->fileUploader = $fileUploader;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $fileId = $this->getRequest()->getParam('param_name') ? : FrontendQuoteUpload::FILE_ID;
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $result = $this->fileUploader
                ->setAllowedExtensions($this->config->getAllowFileExtensions())
                ->saveToTmpFolder($fileId);
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }
        return $resultJson->setData($result);
    }
}
