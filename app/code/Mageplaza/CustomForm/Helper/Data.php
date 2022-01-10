<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Helper;

use DateTime;
use DateTimeZone;
use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filter\TranslitUrl;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Mageplaza\CustomForm\Model\Form as FormModel;
use Mageplaza\CustomForm\Model\FormFactory as CustomFormFactory;
use Mageplaza\CustomForm\Model\Mail;
use Mageplaza\CustomForm\Model\ResourceModel\Form;
use Mageplaza\CustomForm\Model\Responses;

/**
 * Class Data
 * @package Mageplaza\CustomForm\Helper
 */
class Data extends CoreHelper
{
    const CONFIG_MODULE_PATH       = 'mp_custom_form';
    const CONFIG_ADMIN_NOF_PATH    = 'admin_notification';
    const CONFIG_CUSTOMER_NOF_PATH = 'customer_notification';
    const CONFIG_GOOGLE_MAP_PATH   = 'google_map';
    const FILE_MEDIA_PATH          = 'mageplaza/custom_form/tmp';
    const USE_CONFIG_VAL           = '2';

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var EncryptorInterface
     */
    public $enc;

    /**
     * @var TranslitUrl
     */
    protected $transLitUrl;

    /**
     * @var Form
     */
    protected $resourceForm;

    /**
     * @var Mail
     */
    protected $mail;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var CustomFormFactory
     */
    protected $customFormFactory;

    /**
     * @var File
     */
    protected $file;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param EncryptorInterface $enc
     * @param TransportBuilder $transportBuilder
     * @param TranslitUrl $transLitUrl
     * @param Form $resourceForm
     * @param Mail $mail
     * @param Filesystem $filesystem
     * @param CustomFormFactory $customFormFactory
     * @param File $file
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        EncryptorInterface $enc,
        TransportBuilder $transportBuilder,
        TranslitUrl $transLitUrl,
        Form $resourceForm,
        Mail $mail,
        Filesystem $filesystem,
        CustomFormFactory $customFormFactory,
        File $file
    ) {
        $this->transportBuilder  = $transportBuilder;
        $this->enc               = $enc;
        $this->transLitUrl       = $transLitUrl;
        $this->resourceForm      = $resourceForm;
        $this->mail              = $mail;
        $this->filesystem        = $filesystem;
        $this->customFormFactory = $customFormFactory;
        $this->file              = $file;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param string $encodedValue
     *
     * @return mixed
     */
    public function jsDecode($encodedValue)
    {
        return self::jsonDecode($encodedValue);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseTmpMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::FILE_MEDIA_PATH;
    }

    /**
     * @param string $file
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTmpMediaUrl($file)
    {
        return $this->getBaseTmpMediaUrl() . '/' . $this->_prepareFile($file);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAdminNofEnabled($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_ADMIN_NOF_PATH . '/enabled', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAdminNofSendTo($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_ADMIN_NOF_PATH . '/send_to', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAdminNofSender($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_ADMIN_NOF_PATH . '/sender', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAdminNofEmailTemplate($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_ADMIN_NOF_PATH . '/email_template', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAdminCCEmail($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_ADMIN_NOF_PATH . '/send_to_cc', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAdminBCCEmail($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_ADMIN_NOF_PATH . '/send_to_bcc', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAdminAttachedFile($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_ADMIN_NOF_PATH . '/admin_attach_file', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCustomerNofEnabled($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_CUSTOMER_NOF_PATH . '/enabled', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCustomerAttachedFile($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_CUSTOMER_NOF_PATH . '/customer_attach_file', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCustomerNofSender($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_CUSTOMER_NOF_PATH . '/sender', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCustomerNofEmailTemplate($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_CUSTOMER_NOF_PATH . '/email_template', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getGoogleMapApi($storeId = null)
    {
        return $this->enc->decrypt($this->getModuleConfig(self::CONFIG_GOOGLE_MAP_PATH . '/api_key', $storeId));
    }

    /**
     * @param int $storeId
     * @param array $sendTo
     * @param string $template
     * @param array $vars
     * @param string $sender
     * @param bool $isAdmin
     * @param string $ccEmails
     * @param string $bccEmails
     *
     * @throws LocalizedException
     */
    public function sendMail(
        $storeId,
        $sendTo,
        $template,
        $vars,
        $sender = 'general',
        $isAdmin = false,
        $ccEmails = '',
        $bccEmails = ''
    ) {
        $this->mail->setTemplateVars($vars);
        $this->transportBuilder
            ->setTemplateIdentifier($template)
            ->setTemplateOptions([
                'area'  => Area::AREA_FRONTEND,
                'store' => $storeId
            ])->setFrom($sender);
        foreach ($sendTo as $email) {
            $this->transportBuilder->addTo($email);
        }

        if ($isAdmin) {
            if ($ccEmails) {
                $ccEmails = explode(',', $ccEmails);
                $this->transportBuilder->addCc($ccEmails);
            }

            if ($bccEmails) {
                $bccEmails = explode(',', $bccEmails);
                $this->transportBuilder->addBcc($bccEmails);
            }
        }

        $transport = $this->transportBuilder->setTemplateVars($vars)->getTransport();

        try {
            $transport->sendMessage();
        } catch (Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }

    /**
     * Get date formatted
     *
     * @param string $date
     * @param string $dateType
     *
     * @return string
     * @throws Exception
     */
    public function getDateFormat($date, $dateType)
    {
        $dateTime = new DateTime($date, new DateTimeZone('UTC'));
        $dateTime->setTimezone(new DateTimeZone($this->getTimezone()));

        return $dateTime->format($dateType);
    }

    /**
     * get configuration zone
     *
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->getConfigValue('general/locale/timezone');
    }

    /**
     * @param string $identifier
     *
     * @return string
     * @throws LocalizedException
     */
    public function generateUrlKey($identifier)
    {
        $attempt = -1;
        do {
            if ($attempt++ >= 10) {
                throw new LocalizedException(__('Unable to generate url key. Please check the setting and try again.'));
            }

            $urlKey = $this->transLitUrl->filter($identifier);
            if ($urlKey) {
                $urlKey .= ($attempt ?: '');
            }
        } while ($this->checkUrlKey($urlKey));

        return $urlKey;
    }

    /**
     * @param string $urlKey
     *
     * @return bool|string
     * @throws LocalizedException
     */
    public function checkUrlKey($urlKey)
    {
        if (empty($urlKey)) {
            return true;
        }

        $adapter = $this->resourceForm->getConnection();
        $select  = $adapter->select()
            ->from($this->resourceForm->getMainTable(), '*')
            ->where('identifier = :url_key');

        $binds = ['url_key' => (string) $urlKey];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @param Responses $response
     *
     * @return array
     */
    public function getFileFromResponse($response)
    {
        $data          = $this->prepareCustomFormData($response);
        $attachedFiles = [];

        foreach ($data as $page) {
            if (!empty($page['field_groups'])) {
                foreach ((array) $page['field_groups'] as $fieldGroup) {
                    if (!empty($fieldGroup['fields'])) {
                        foreach ((array) $fieldGroup['fields'] as $field) {
                            if ($field['type'] === 'upload') {
                                $path            = $field['chose_value'];
                                $directory       = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                                $fileName        = Data::FILE_MEDIA_PATH . '/' . ltrim($path, '/');
                                $attachedFiles[] = [
                                    'name'  => $this->file->getPathInfo($path)['basename'],
                                    'value' => $directory->getAbsolutePath($fileName)
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $attachedFiles;
    }

    /**
     * @param Responses $response
     *
     * @return array
     */
    public function prepareCustomFormData($response)
    {
        $responseData   = Data::jsonDecode($response->getFormData());
        $customForm     = $this->getCustomForm($response->getFormId());
        $customFormData = Data::jsonDecode($customForm->getCustomForm());
        if (!empty($customFormData) && is_array($customFormData)) {
            foreach ($customFormData as $pageId => &$page) {
                if (!empty($page['field_groups']) && is_array($page['field_groups'])) {
                    foreach ($page['field_groups'] as $fieldGroupId => &$fieldGroup) {
                        if (!empty($fieldGroup['fields']) && is_array($fieldGroup['fields'])) {
                            foreach ($fieldGroup['fields'] as $fieldId => &$field) {
                                $field['chose_value'] =
                                    isset($responseData[$pageId]['fieldGroups'][$fieldGroupId]['fields'][$fieldId]) ?
                                        $responseData[$pageId]['fieldGroups'][$fieldGroupId]['fields'][$fieldId] :
                                        null;
                            }
                            unset($field);
                        }
                    }
                    unset($fieldGroup);
                }
            }
            unset($page);
        }

        return $customFormData;
    }

    /**
     * @param int|string $formId
     *
     * @return FormModel
     */
    public function getCustomForm($formId)
    {
        $customForm = $this->customFormFactory->create();
        $this->resourceForm->load($customForm, $formId);

        return $customForm;
    }

    /**
     * @param null $format
     *
     * @return array
     */
    public function getDateRange($format = null)
    {
        try {
            if ($dateRange = $this->_request->getParam('dateRange')) {
                $startDate        = $format ? $this->formatDate($format, $dateRange[0]) : $dateRange[0];
                $endDate          = $format ? $this->formatDate($format, $dateRange[1]) : $dateRange[1];
            } else {
                list($startDate, $endDate) = $this->getDateTimeRangeFormat('-1 month', 'now', null, $format);
            }
        } catch (Exception $e) {
            $this->_logger->critical($e);

            return [null, null];
        }

        return [$startDate, $endDate];
    }

    /**
     * @param string $startDate
     * @param null $endDate
     * @param null $isConvertToLocalTime
     *
     * @param null $format
     *
     * @return array
     * @throws Exception
     */
    public function getDateTimeRangeFormat($startDate, $endDate = null, $isConvertToLocalTime = null, $format = null)
    {
        $endDate   = (new DateTime($endDate ?: $startDate, new DateTimeZone($this->getTimezone())))->setTime(
            23,
            59,
            59
        );
        $startDate = (new DateTime($startDate, new DateTimeZone($this->getTimezone())))->setTime(00, 00, 00);

        if ($isConvertToLocalTime) {
            $startDate->setTimezone(new DateTimeZone('UTC'));
            $endDate->setTimezone(new DateTimeZone('UTC'));
        }

        return [$startDate->format($format ?: 'Y-m-d H:i:s'), $endDate->format($format ?: 'Y-m-d H:i:s')];
    }
}
