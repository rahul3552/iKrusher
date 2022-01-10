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

namespace Mageplaza\ProductAttachments\Controller\Adminhtml\Product\Grid;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Model\File;
use Mageplaza\ProductAttachments\Model\FileFactory;
use RuntimeException;

/**
 * Class Save
 * @package Mageplaza\ProductAttachments\Controller\Adminhtml\Product\Grid
 */
class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Catalog::products';
    const ENABLE = '1';
    const DISABLE = '0';
    const DEFAULT_LOCATION = 'default';
    const USE_SYSTEM_CONFIG = 'use_system_config';

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var Session
     */
    protected $_backendSession;

    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param DateTime $dateTime
     * @param Product $product
     * @param FileFactory $fileFactory
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        DateTime $dateTime,
        Product $product,
        FileFactory $fileFactory,
        Data $helperData
    ) {
        $this->_resultPageFactory = $pageFactory;
        $this->_dateTime = $dateTime;
        $this->_messageManager = $context->getMessageManager();
        $this->_backendSession = $context->getSession();
        $this->_product = $product;
        $this->_fileFactory = $fileFactory;
        $this->_helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $currentProductId = (int)$data['product_id'];
        $product = $this->_product->load($currentProductId);
        $attachmentsLocation = [];

        if (isset($data['product']['mpattachments']['attachment_location']['use_system_config'])
            && $data['product']['mpattachments']['attachment_location']['use_system_config']
        ) {
            $attachmentsLocation = $this->_helperData->getConfigGeneral('show_on');
        } else {
            if (isset($data['product']['mpattachments']['attachment_location'])) {
                foreach ($data['product']['mpattachments']['attachment_location'] as $key => $location) {
                    if ($location === self::ENABLE) {
                        if ($key === self::USE_SYSTEM_CONFIG) {
                            $attachmentsLocation[] = '';
                            break;
                        }
                        $attachmentsLocation[] = ($key === self::DEFAULT_LOCATION) ? '' : (int)$key;
                    }
                }
            }

            $attachmentsLocation = implode(',', $attachmentsLocation);
        }

        if (!$attachmentsLocation) {
            $attachmentsLocation = 0;
        }

        $product->setCustomAttribute(Data::ATTACHMENTS_LOCATION_ATTRIBUTE_CODE, $attachmentsLocation)->save();

        if (isset($data['product']['mpattachments']['images'])) {
            foreach ($data['product']['mpattachments']['images'] as $image) {
                /** Add new file */
                if (!empty($image['is_new']) && empty($image['removed'])) {
                    $this->saveFile($image, $currentProductId);
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
                        $file->load((int)$image['value_id']);
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

    /**
     * @param $data
     * @param $currentProductId
     */
    public function saveFile($data, $currentProductId)
    {
        /** @var File $file */
        $file = $this->_fileFactory->create();
        $file->setProductId($currentProductId);
        $data['created_at'] = $this->_dateTime->date();
        try {
            $file->addData($data)->save();
        } catch (RuntimeException $e) {
            $this->_messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while saving the File.')
            );
        }
    }
}
