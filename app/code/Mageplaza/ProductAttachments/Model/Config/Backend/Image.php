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
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Model\Config\Backend;

use Exception;
use Magento\Config\Model\Config\Backend\File;
use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Mageplaza\ProductAttachments\Helper\Data as HelperData;

/**
 * Class Image
 * @package Mageplaza\ProductAttachments\Model\Config\Backend
 */
class Image extends File
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Image constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param UploaderFactory $uploaderFactory
     * @param RequestDataInterface $requestData
     * @param Filesystem $filesystem
     * @param HelperData $helperData
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        UploaderFactory $uploaderFactory,
        RequestDataInterface $requestData,
        Filesystem $filesystem,
        HelperData $helperData,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_helperData = $helperData;

        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $uploaderFactory,
            $requestData,
            $filesystem,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['svg', 'jpg', 'jpeg', 'gif', 'png'];
    }

    /**
     * @return $this|void
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $configIcon = [];
        $value = $this->getValue();
        unset($value['__empty']);
        foreach ($value as $item) {
            $item['file_type'] = strtolower($item['file_type']);
            $configIcon[] = $item['file_type'];
        }
        if (count($configIcon) !== count(array_unique($configIcon))) {
            throw new LocalizedException(__('Duplicate icon type name. Please try again.'));
        }
        foreach ($value as $key => $item) {
            $item['file_type'] = strtolower($item['file_type']);
            $item['file_icon']['value'] = $item['file_icon_hidden'];
            $file = $item['file_icon'];

            if (isset($file['value']) && $file['value'] && !$file['name']) {
                $item['file_icon'] = $file['value'];
            } elseif (!empty($file)) {
                $uploadDir = $this->_getUploadDir();
                /** @var Uploader $uploader */
                $uploader = $this->_uploaderFactory->create(['fileId' => $file]);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                if (!$uploader->checkAllowedExtension($uploader->getFileExtension())) {
                    throw new LocalizedException(__('We don\'t recognize or support this file extension type.'));
                }
                $uploader->setAllowRenameFiles(true);
                $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                try {
                    $result = $uploader->save($uploadDir);
                    $fileName = $result['file'];
                } catch (Exception $e) {
                    throw new LocalizedException(__('File you are trying to upload exceeds maximum file size limit.'));
                }

                if ($fileName) {
                    if ($this->_addWhetherScopeInfo()) {
                        $fileName = $this->_prependScopeInfo($fileName);
                    }
                    $item['file_icon'] = $fileName;
                }
            } elseif (is_array($item) && !empty($item['delete'])) {
                $item['file_icon'] = '';
            } else {
                unset($item['file_icon']);
            }
            $value[$key] = $item;
        }
        $encodedValue = $this->_helperData->serialize($value);
        $this->setValue($encodedValue);
    }

    /**
     * Process data after load
     *
     * @return $this|void
     */
    protected function _afterLoad()
    {
        /** @var string $value */
        $value = $this->getValue();
        $decodedValue = $this->_helperData->unserialize($value);
        $this->setValue($decodedValue);
    }
}
