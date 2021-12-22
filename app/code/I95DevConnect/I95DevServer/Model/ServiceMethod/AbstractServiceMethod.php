<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_I95DevServer
 */
namespace I95DevConnect\I95DevServer\Model\ServiceMethod;

/**
 * Abstract class for service method
 */
class AbstractServiceMethod
{

    /**
     * @var \I95DevConnect\MessageQueue\Model\I95DevResponse
     */
    public $i95DevResponse;

    /**
     * @var \I95DevConnect\MessageQueue\Model\Logger
     */
    public $logger;

    public $erpName;

    public $currentMethodProperties = [];

    public $currententitiyCode;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfigInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * Constructor for DI
     * @param \I95DevConnect\MessageQueue\Model\Logger $logger
     * @param \I95DevConnect\MessageQueue\Model\I95DevResponse $i95DevResponse
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Model\Logger $logger,
        \I95DevConnect\MessageQueue\Model\I95DevResponse $i95DevResponse,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->i95DevResponse = $i95DevResponse;
        $this->logger = $logger;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->storeManager = $storeManager;
    }

    /**
     * Method to decode json string
     * @param string $inputString
     * @return array
     */
    public function convertInputStringToArray($inputString)
    {
        return json_decode($inputString, 1);
    }

    /**
     * Method to get current entity properties
     *
     * @param string $entityCode
     *
     * @return bool
     */
    public function getCurrentEnityProperties($entityCode)
    {
        $status = false;
        $this->currententitiyCode = $this->currentMethodProperties['entityCode'];

        if (isset($this->dataHelper->entityList[$this->currententitiyCode])) {
            $this->currentEntityProperties = $this->dataHelper->entityList[$entityCode];
            $status = true;
        }

        return $status;
    }

    /**
     * Method to prepare response object
     * @param string $status
     * @param string $message
     * @param string $responseData
     * @return object
     */
    public function setResponse($status, $message = null, $responseData = null)
    {
        $this->i95DevResponse->setResult($status);
        $this->i95DevResponse->setResultdata($responseData);
        $this->i95DevResponse->setMessage($message);

        return $this->i95DevResponse;
    }

    /**
     * Adds AES 256 Decryption
     * @parameter string
     * @param string $string
     * @return Decrypted String
     */
    public function decryptDES($string = '')
    {
        // phpcs:disable
        $decryptString = "";
        $key = $this->getEncryptionPass();
        $key = substr(hash('sha256', $key, true), 0, 32); // NOSONAR
        $iv = base64_decode('alpqbmVOYmE3OHRxQ3VCOA==');
        if (is_string($string)) {
            $string = base64_decode($string);
            $decryptString = openssl_decrypt($string, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        }

        // phpcs:enable
        return $decryptString;
    }

    /**
     * Adds AES 256 Encryption
     * @parameter string
     * @param string $string
     * @return Encrypted String
     */
    public function encryptAES($string = "")
    {
        // phpcs:disable
        $key = $this->getEncryptionPass();
        $key = substr(hash('sha256', $key, true), 0, 32); // NOSONAR
        $iv = base64_decode('alpqbmVOYmE3OHRxQ3VCOA==');
        // phpcs:enable
        return base64_encode(openssl_encrypt( //NOSONAR
            $string,
            'aes-256-cbc',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        ));
    }

    /**
     * @return mixed
     */
    public function getEncryptionPass()
    {
        return $this->scopeConfigInterface->getValue(
            'i95dev_messagequeue/I95DevConnect_credentials/encryption_pass',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }
}
