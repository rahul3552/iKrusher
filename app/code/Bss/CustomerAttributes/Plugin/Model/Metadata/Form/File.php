<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 *
 * @category   BSS
 * @package    Bss_CustomerAttributes
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomerAttributes\Plugin\Model\Metadata\Form;

use Magento\Customer\Model\FileProcessor;
use Magento\Customer\Model\FileProcessorFactory;
use Magento\Customer\Model\Metadata\Form\AbstractData;
use Magento\Framework\Api\ArrayObjectSearch;
use Magento\Framework\Api\Data\ImageContentInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File as IoFile;

/**
 * Processes files that are save for customer.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * // @codingStandardsIgnoreFile
 */
class File extends AbstractData
{
    /**
     * @var \Bss\CustomerAttributes\Helper\Customerattribute
     */
    protected $customerattribute;

    /**
     * @var \Bss\CustomerAttributes\Helper\File
     */
    protected $helperFile;

    /**
     * @var IoFile
     */
    protected $ioFile;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Core data
     *
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension
     */
    protected $fileValidator;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * @var FileProcessorFactory
     * @deprecated 101.0.0
     */
    protected $fileProcessorFactory;

    /**
     * Constructor
     *
     * @param \Bss\CustomerAttributes\Helper\Customerattribute $customerattribute
     * @param \Bss\CustomerAttributes\Helper\File $helperFile
     * @param IoFile $ioFile
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param string|array|bool $value
     * @param string $entityTypeCode
     * @param bool $isAjax
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $fileValidator
     * @param Filesystem $fileSystem
     * @param UploaderFactory $uploaderFactory
     * @param \Magento\Customer\Model\FileProcessorFactory|null $fileProcessorFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @codingStandardsIgnoreStart
     */
    public function __construct(
        \Bss\CustomerAttributes\Helper\Customerattribute $customerattribute,
        \Bss\CustomerAttributes\Helper\File $helperFile,
        IoFile $ioFile,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        $value,
        $entityTypeCode,
        $isAjax,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $fileValidator,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        \Magento\Customer\Model\FileProcessorFactory $fileProcessorFactory = null
    ) {
        $this->customerattribute = $customerattribute;
        $this->helperFile = $helperFile;
        $this->ioFile = $ioFile;
        $this->request = $request;
        parent::__construct($localeDate, $logger, $attribute, $localeResolver, $value, $entityTypeCode, $isAjax);
        $this->urlEncoder = $urlEncoder;
        $this->fileValidator = $fileValidator;
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileProcessorFactory = $fileProcessorFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Customer\Model\FileProcessorFactory::class);
        $this->fileProcessor = $this->fileProcessorFactory->create(['entityTypeCode' => $this->_entityTypeCode]);
    }
    // @codingStandardsIgnoreEnd

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @codingStandardsIgnoreStart
     */
    public function extractValue(\Magento\Framework\App\RequestInterface $request)
    {
        $files = $this->request->getFiles()->toArray();
        $extend = $this->_getRequestValue($request);

        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($this->_requestScope || !isset($files[$attrCode])) {
            $value = [];
            if (strpos($this->_requestScope, '/') !== false) {
                $scopes = explode('/', $this->_requestScope);
                $mainScope = array_shift($scopes);
            } else {
                $mainScope = $this->_requestScope;
                $scopes = [];
            }

            if (!empty($files[$mainScope])) {
                foreach ($files[$mainScope] as $fileKey => $scopeData) {
                    foreach ($scopes as $scopeName) {
                        if (isset($scopeData[$scopeName])) {
                            $scopeData = $scopeData[$scopeName];
                        } else {
                            $scopeData[$scopeName] = [];
                        }
                    }

                    if (isset($scopeData[$attrCode])) {
                        $value[$fileKey] = $scopeData[$attrCode];
                    }
                }
            } elseif (isset($extend[0]['file']) && !empty($extend[0]['file'])) {
                /**
                 * This case is required by file uploader UI component
                 *
                 * $extend[0]['file'] - uses for AJAX validation
                 * $extend[0] - uses for POST request
                 */
                $value = $this->getIsAjaxRequest() ? $extend[0]['file'] : $extend[0];
            } else {
                $value = [];
            }
        } else {
            if (isset($files[$attrCode])) {
                $value = $files[$attrCode];
            } else {
                $value = [];
            }
        }

        if (!empty($extend['delete'])) {
            $value['delete'] = true;
        }

        return $value;
    }
    // @codingStandardsIgnoreEnd

    /**
     * Validate file by attribute validate rules. Returns array of errors.
     *
     * @param array $value
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _validateByRules($value)
    {
        $label = $value['name'];
        $rules = $this->getAttribute()->getValidationRules();
        $extension = pathinfo($value['name'], PATHINFO_EXTENSION);
        $fileExtensions = ArrayObjectSearch::getArrayElementByName(
            $rules,
            'file_extensions'
        );
        if ($fileExtensions !== null) {
            $extensions = explode(',', $fileExtensions);
            $extensions = array_map('trim', $extensions);
            if (!in_array($extension, $extensions)) {
                return [__('"%1" is not a valid file extension.', $extension)];
            }
        }

        /**
         * Check protected file extension
         */
        if (!$this->fileValidator->isValid($extension)) {
            return $this->fileValidator->getMessages();
        }

        if (!$this->_isUploadedFile($value['tmp_name'])) {
            return [__('"%1" is not a valid file.', $label)];
        }

        $maxFileSize = ArrayObjectSearch::getArrayElementByName(
            $rules,
            'max_file_size'
        );
        if ($maxFileSize !== null) {
            $maxFileSize = $maxFileSize * 1000;
            $size = $value['size'];
            if ($maxFileSize < $size) {
                return [__('"%1" exceeds the allowed file size.', $label)];
            }
        }

        return [];
    }

    /**
     * Helper function that checks if the file was uploaded.
     *
     * This helper function is needed for testing.
     *
     * @param string $filename
     * @return bool
     */
    protected function _isUploadedFile($filename)
    {
        if (is_uploaded_file($filename)) {
            return true;
        }

        // This case is required for file uploader UI component
        $temporaryFile = FileProcessor::TMP_DIR . '/' . $this->ioFile->getPathInfo($filename)['basename'];
        if ($this->fileProcessor->isExist($temporaryFile)) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validateValue($value)
    {
        if ($this->getIsAjaxRequest()) {
            return true;
        }

        if ($this->customerattribute->isDisableAttributeAddress($this->getAttribute())) {
            return true;
        }

        $errors = [];
        $attribute = $this->getAttribute();
        $label = $attribute->getStoreLabel();

        $toDelete = !empty($value['delete']) ? true : false;
        $toUpload = !empty($value['tmp_name']) ? true : false;

        if (!$toUpload && !$toDelete && $this->_value) {
            return true;
        }

        if (!$attribute->isRequired() && !$toUpload) {
            return true;
        }

        if ($attribute->isRequired() && !$toUpload) {
            $files = $this->request->getFiles();
            $attributeCode = $this->getAttribute()->getAttributeCode();
            if (!empty($attributeCode) && isset($files["order"]) &&
                isset($files["order"]["billing_address"]) &&
                isset($files["order"]["billing_address"][$attributeCode]) &&
                isset($files["order"]["billing_address"][$attributeCode]["tmp_name"])
            ) {
                return true;
            }
            $errors[] = __('"%1" is a required value.', $label);
        }

        if ($toUpload) {
            $errors = array_merge($errors, $this->_validateByRules($value));
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

    /**
     * @inheritdoc
     *
     * @param string|array $value
     * @return ImageContentInterface|array|string|null
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function compactValue($value)
    {
        if ($this->getIsAjaxRequest()) {
            return $this;
        }

        // Remove outdated file (in the case of file uploader UI component)
        if (empty($value) && !empty($this->_value)) {
            $this->removeFileOutdated();
            return $value;
        }

        if (isset($value['file']) && !empty($value['file'])) {
            if ($value['file'] == $this->_value) {
                return $this->_value;
            }
            $result = $this->processUiComponentValue($value);
        } else {
            $result = $this->processInputFieldValue($value);
        }

        return $result;
    }

    /**
     * Remove outdated file (in the case of file uploader UI component)
     */
    public function removeFileOutdated()
    {
        $thisValue = "";
        if (is_string($this->_value)) {
            $thisValue = $this->_value;
        } elseif (isset($this->_value["value"]) && is_string($this->_value["value"])) {
            $thisValue = $this->_value["value"];
        }
        if ($thisValue) {
            $this->fileProcessor->removeUploadedFile($thisValue);
        }
    }

    /**
     * Process file uploader UI component data
     *
     * @param array $value
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processUiComponentValue(array $value)
    {
        $result = $this->fileProcessor->moveTemporaryFile($value['file']);
        return $result;
    }

    /**
     * Process input type=file component data
     *
     * @param array $value
     * @return bool|int|string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function processInputFieldValue($value)
    {
        $result = $this->_value;
        $addressType = $this->helperFile->addressType;

        if ($addressType && isset($value[$addressType])) {
            $value = $value[$addressType];
        }

        $mediaDir = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
        if (!empty($value['tmp_name'])) {
            try {
                $uploader = $this->uploaderFactory->create(['fileId' => $value]);
                $uploader->setFilesDispersion(true);
                $uploader->setFilenamesCaseSensitivity(false);
                $uploader->setAllowRenameFiles(true);
                $uploader->save($mediaDir->getAbsolutePath($this->_entityTypeCode), $value['name']);
                $result = $uploader->getUploadedFileName();
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function restoreValue($value)
    {
        return $this->_value;
    }

    /**
     * @inheritdoc
     */
    public function outputValue($format = \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_TEXT)
    {
        $output = '';
        if ($this->_value) {
            switch ($format) {
                case \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_JSON:
                    $output = ['value' => $this->_value, 'url_key' => $this->urlEncoder->encode($this->_value)];
                    break;
            }
        }

        return $output;
    }

    /**
     * Get file processor
     *
     * @return FileProcessor
     * @deprecated 100.1.3
     */
    protected function getFileProcessor()
    {
        return $this->fileProcessor;
    }
}
