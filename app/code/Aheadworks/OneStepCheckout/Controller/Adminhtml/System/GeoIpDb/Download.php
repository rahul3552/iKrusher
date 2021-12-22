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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Controller\Adminhtml\System\GeoIpDb;

use Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\DatabaseDownloader;
use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;

/**
 * Class Download
 * @package Aheadworks\OneStepCheckout\Controller\Adminhtml\System\GeoIpDb
 * phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
 */
class Download extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_OneStepCheckout::config_aw_osc';

    /**
     * @var DatabaseDownloader
     */
    private $downloader;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param DatabaseDownloader $downloader
     * @param Config $config
     */
    public function __construct(
        Context $context,
        DatabaseDownloader $downloader,
        Config $config
    ) {
        parent::__construct($context);
        $this->downloader = $downloader;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $requestData = $this->getRequest()->getPostValue();
        $responseData = [];
        if ($this->isValid($requestData)) {
            set_time_limit(0);

            try {
                $info = $this->downloader->downloadAndUnpack(
                    $requestData['file_name'],
                    $requestData['archive_name']
                );
                $responseData['success'] = true;
                $responseData['updated_at'] = $info['modified_at'];
            } catch (\Exception $e) {
                $responseData['success'] = false;
                $responseData['error'] = $e->getMessage();
            }

            ini_restore('max_execution_time');
        } else {
            $responseData['success'] = false;
        }

        return $resultJson->setData($responseData);
    }

    /**
     * Check if request data is valid
     *
     * @param array $requestData
     * @return bool
     */
    private function isValid($requestData)
    {
        return $this->config->getLicenseKey()
            && isset($requestData['file_name']);
    }
}
