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

namespace Mageplaza\ProductAttachments\Observer\Product;

use Exception;
use Magento\Backend\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Model\File;
use Mageplaza\ProductAttachments\Model\FileFactory;
use RuntimeException;

/**
 * Class Save
 * @package Mageplaza\ProductAttachments\Observer\Product
 */
class Save implements ObserverInterface
{
    const ENABLE            = '1';
    const DISABLE           = '0';
    const DEFAULT_LOCATION  = 'default';
    const USE_SYSTEM_CONFIG = 'use_system_config';

    /**
     * @var Session
     */
    protected $_backendSession;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * Save constructor.
     *
     * @param Session $session
     * @param ManagerInterface $messageManager
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Session $session,
        ManagerInterface $messageManager,
        DateTime $dateTime,
        FileFactory $fileFactory
    ) {
        $this->_backendSession = $session;
        $this->_messageManager = $messageManager;
        $this->_dateTime       = $dateTime;
        $this->_fileFactory    = $fileFactory;
    }

    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $data                = $observer->getController()->getRequest()->getPostValue();
        $product             = $observer->getProduct();
        $attachmentsLocation = [];

        if (isset($data['product']['mpattachments']['attachment_location'])) {
            foreach ($data['product']['mpattachments']['attachment_location'] as $key => $location) {
                if ($location === self::ENABLE) {
                    if ($key === self::USE_SYSTEM_CONFIG) {
                        $attachmentsLocation[] = '';
                        break;
                    }
                    $attachmentsLocation[] = ($key === self::DEFAULT_LOCATION) ? '' : (int) $key;
                }
            }
        }
        $attachmentsLocation = implode(',', $attachmentsLocation);
        $product->addAttributeUpdate(
            Data::ATTACHMENTS_LOCATION_ATTRIBUTE_CODE,
            $attachmentsLocation,
            $product->getStoreId()
        );

        $currentProductId = (int) $product->getId();

        if (isset($data['product']['mpattachments']['images'])) {
            foreach ($data['product']['mpattachments']['images'] as $image) {
                /** Add new file */
                if (!empty($image['is_new']) && empty($image['removed'])) {
                    try {
                        /** @var File $file */
                        $file = $this->_fileFactory->create();
                        $file->setProductId($currentProductId);
                        $image['created_at'] = $this->_dateTime->date();
                        $file->addData($image)->save();
                    } catch (LocalizedException $e) {
                        $this->_messageManager->addErrorMessage($e->getMessage());
                    } catch (RuntimeException $e) {
                        $this->_messageManager->addErrorMessage($e->getMessage());
                    } catch (Exception $e) {
                        $this->_messageManager->addExceptionMessage(
                            $e,
                            __('Something went wrong while saving the File.')
                        );
                    }
                } elseif (!empty($image['is_updated'])) {
                    /** Update file */
                    try {
                        /** @var File $file */
                        $file = $this->_fileFactory->create();
                        $file->getResource()->updateData($image);
                    } catch (LocalizedException $e) {
                        $this->_messageManager->addErrorMessage($e->getMessage());
                    } catch (RuntimeException $e) {
                        $this->_messageManager->addErrorMessage($e->getMessage());
                    } catch (Exception $e) {
                        $this->_messageManager->addExceptionMessage(
                            $e,
                            __('Something went wrong while updating the File.')
                        );
                    }
                }
                /** Delete file */
                if (!empty($image['removed'])) {
                    try {
                        /** @var File $file */
                        $file = $this->_fileFactory->create();
                        $file->load((int) $image['value_id']);
                        $file->delete();
                    } catch (LocalizedException $e) {
                        $this->_messageManager->addErrorMessage($e->getMessage());
                    } catch (RuntimeException $e) {
                        $this->_messageManager->addErrorMessage($e->getMessage());
                    } catch (Exception $e) {
                        $this->_messageManager->addExceptionMessage(
                            $e,
                            __('Something went wrong while deleting the File.')
                        );
                    }
                }
            }
            $this->_backendSession->unsFileData();
        }
    }
}
