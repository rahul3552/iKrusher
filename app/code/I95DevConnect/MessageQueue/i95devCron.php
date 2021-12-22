<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */
// phpcs:disable
use Magento\Framework\App\Bootstrap;

$dateFormate = 'Y-m-d H:i:s';

$dirPath =  __DIR__;
$dirpath = str_replace("app/code/I95DevConnect/MessageQueue", "", $dirPath);

require $dirpath . '/app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

$appState = $obj->get("\Magento\Framework\App\State");
$appState->setAreaCode("global");

$storeManager = $obj->get("\Magento\Store\Model\StoreManagerInterface");
$productMetadata = $obj->get("\Magento\Framework\App\ProductMetadataInterface");
$magentoVersion = $productMetadata->getVersion();

$fileSystem = $obj->create("\Magento\Framework\Filesystem\Driver\File");

$baseUrl = $storeManager->getStore()->getBaseUrl();

if (version_compare($magentoVersion, '2.4', '>=')) {
    $baseUrl = str_replace("i95devCron.php/", "", $baseUrl);
}

try {
    $filename1 = $dirpath . "/var/log/Cronruning.log";

    $fp = $fileSystem->fileOpen($filename1, "a");
    $fileSystem->fileWrite($fp, "Cron Started " . date($dateFormate) . "\n");
    $fileSystem->fileClose($fp);


    /**
     * @author Sravani Polu
     * Changing object call to API to set user context
     */
    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;

    $scopeConfig = $obj->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);
    $token = $scopeConfig->getValue(
        'i95dev_messagequeue/I95DevConnect_credentials/token',
        $storeScope,
        $storeManager->getDefaultStoreView()->getWebsiteId()
    );


    $curl = $obj->get(\Magento\Framework\HTTP\Client\Curl::class);
    $curl->setHeaders(['Content-Type' => 'application/json',
        'charset' => 'utf-8',
        'Authorization' => 'Bearer ' . $token]);
    $curl->setOption(CURLOPT_POST, 1);
    $curl->setOption(CURLOPT_POSTFIELDS, []);
    $curl->setOption(CURLINFO_SSL_ENGINES, 1);
    $curl->setOption(CURLOPT_RETURNTRANSFER, 1);
    $finalUrl = $baseUrl . 'rest/V1/ReverseSyncService/?methodName=syncMQtoMagento';
    $curl->post($finalUrl, []);
    $response = $curl->getBody();

    $fp = $fileSystem->fileOpen($filename1, "a");
    $fileSystem->fileWrite($fp, "Cron End " . date($dateFormate) . "\n");
    $fileSystem->fileClose($fp);
} catch (\Magento\Framework\Exception\LocalizedException $ex) {
    $fp = $fileSystem->fileClose($filename1, "a");
    $fileSystem->fileWrite($fp, $ex->getMessage() . "\n");
    $fileSystem->fileWrite($fp, "Cron End " . date($dateFormate) . "\n");
    $fileSystem->fileClose($fp);
}
// phpcs:enable
