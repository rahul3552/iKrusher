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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Model\Import;

use Exception;
use Magento\CatalogImportExport\Model\Import\UploaderFactory;
use Magento\CatalogRule\Model\Rule\Condition\Combine;
use Magento\CatalogRule\Model\Rule\Condition\Product;
use Magento\Customer\Model\GroupFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Helper\Data as ImportHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper as ResourceHelper;
use Magento\ImportExport\Model\ResourceModel\Import\Data as ImportData;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ProductAttachments\Helper\Data as HelperData;
use Mageplaza\ProductAttachments\Model\Config\Source\FileAction;
use Mageplaza\ProductAttachments\Model\Config\Source\Icon;
use Mageplaza\ProductAttachments\Model\FileFactory;
use Mageplaza\ProductAttachments\Model\Import\File\RowValidatorInterface as ValidatorInterface;
use Zend_Serializer_Exception;

/**
 * Class File
 * @package Mageplaza\ProductAttachments\Model\Import
 */
class File extends AbstractEntity
{
    const COL_LABEL                = 'label';
    const COL_NAME                 = 'name';
    const COL_STATUS               = 'status';
    const COL_STORE_ID             = 'store_ids';
    const COL_CUSTOMER_GROUP       = 'customer_group';
    const COL_GROUP                = 'group';
    const COL_SIZE                 = 'size';
    const COL_FILE_PATH            = 'file_path';
    const COL_FILE_ICON_PATH       = 'file_icon_path';
    const COL_CUSTOMER_LOGIN       = 'customer_login';
    const COL_IS_BUYER             = 'is_buyer';
    const COL_FILE_ACTION          = 'file_action';
    const COL_PRIORITY             = 'priority';
    const COL_POSITION             = 'position';
    const COL_CREATED_AT           = 'created_at';
    const COL_IS_GRID              = 'is_grid';
    const COL_PRODUCT_SKU          = 'product_sku';
    const COL_CONDITION            = 'conditions_serialized';
    const MP_ATTACHMENT_TABLE_NAME = 'mageplaza_productattachments_file';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_LABEL_IS_EMPTY => 'File label is empty',
    ];

    /**
     * @var array
     */
    protected $_permanentAttributes = [self::COL_LABEL, self::COL_NAME, self::COL_FILE_PATH];

    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * @var GroupFactory
     */
    protected $groupFactory;

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var StringUtils
     */
    protected $_string;

    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
        self::COL_LABEL,
        self::COL_NAME,
        self::COL_STATUS,
        self::COL_STORE_ID,
        self::COL_CUSTOMER_GROUP,
        self::COL_GROUP,
        self::COL_SIZE,
        self::COL_FILE_PATH,
        self::COL_FILE_ICON_PATH,
        self::COL_CUSTOMER_LOGIN,
        self::COL_IS_BUYER,
        self::COL_FILE_ACTION,
        self::COL_PRIORITY,
        self::COL_PRODUCT_SKU,
    ];

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * @var array
     */
    protected $_validators = [];

    /**
     * @var DateTime
     */
    protected $_connection;

    /**
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var array
     */
    protected $_fileUploader;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @var Filesystem
     */
    protected $_mediaDirectory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var Icon
     */
    protected $_iconList;

    /**
     * @var FileAction
     */
    protected $_fileAction;

    /**
     * @var FileFactory
     */
    protected $_fileModel;

    /**
     * File constructor.
     *
     * @param JsonHelper $jsonHelper
     * @param ImportHelper $importExportData
     * @param ImportData $importData
     * @param Config $config
     * @param ResourceConnection $resource
     * @param ResourceHelper $resourceHelper
     * @param StringUtils $string
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param GroupFactory $groupFactory
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param HelperData $helperData
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param Icon $iconList
     * @param FileAction $fileAction
     * @param FileFactory $fileModel
     *
     * @throws FileSystemException
     */
    public function __construct(
        JsonHelper $jsonHelper,
        ImportHelper $importExportData,
        ImportData $importData,
        Config $config,
        ResourceConnection $resource,
        ResourceHelper $resourceHelper,
        StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        GroupFactory $groupFactory,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        HelperData $helperData,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        Icon $iconList,
        FileAction $fileAction,
        FileFactory $fileModel
    )
    {
        $this->jsonHelper        = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper   = $resourceHelper;
        $this->_dataSourceModel  = $importData;
        $this->_resource         = $resource;
        $this->_string           = $string;
        $this->_config           = $config;
        $this->_connection       = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator   = $errorAggregator;
        $this->groupFactory      = $groupFactory;
        $this->_storeManager     = $storeManager;
        $this->_dateTime         = $dateTime;
        $this->_helperData       = $helperData;
        $this->_uploaderFactory  = $uploaderFactory;
        $this->_mediaDirectory   = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_iconList         = $iconList;
        $this->_fileAction       = $fileAction;
        $this->_fileModel        = $fileModel;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'mageplaza_productattachments_file';
    }

    /**
     * Validate data.
     *
     * @return ProcessingErrorAggregatorInterface
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validateData()
    {
        if (!$this->_dataValidated) {
            $this->getErrorAggregator()->clear();
            /** do all permanent columns exist? */
            $absentColumns = array_diff($this->_permanentAttributes, $this->getSource()->getColNames());
            $this->addErrors(self::ERROR_CODE_COLUMN_NOT_FOUND, $absentColumns);

            if (Import::BEHAVIOR_DELETE !== $this->getBehavior()) {
                /** check attribute columns names validity */
                $columnNumber       = 0;
                $emptyHeaderColumns = [];
                $invalidColumns     = [];
                $invalidAttributes  = [];
                foreach ($this->getSource()->getColNames() as $columnName) {
                    $columnNumber++;
                    if (!$this->isAttributeParticular($columnName)) {
                        if (!trim($columnName)) {
                            $emptyHeaderColumns[] = $columnNumber;
                        } elseif (!preg_match('/^[a-z][a-z0-9_]*$/', $columnName)) {
                            $invalidColumns[] = $columnName;
                        } elseif ($this->needColumnCheck && !in_array($columnName, $this->getValidColumnNames())) {
                            $invalidAttributes[] = $columnName;
                        }
                    }
                }
                if (isset($invalidAttributes)) {
                    foreach ($invalidAttributes as $invalidAttribute) {
                        $this->addErrors(
                            self::ERROR_CODE_COLUMN_NAME_INVALID . ': ' . $invalidAttribute,
                            $invalidAttributes
                        );
                    }
                }

                $this->addErrors(self::ERROR_CODE_COLUMN_EMPTY_HEADER, $emptyHeaderColumns);
                $this->addErrors(self::ERROR_CODE_COLUMN_NAME_INVALID, $invalidColumns);
            }

            if (!$this->getErrorAggregator()->getErrorsCount()) {
                $this->_saveValidatedBunches();
                $this->_dataValidated = true;
            }
        }

        return $this->getErrorAggregator();
    }

    /**
     * Create Advanced price data from raw data.
     *
     * @return bool Result of operation.
     * @throws Exception
     */
    protected function _importData()
    {
        if (Import::BEHAVIOR_DELETE === $this->getBehavior()) {
            $this->deleteEntity();
        } elseif (Import::BEHAVIOR_REPLACE === $this->getBehavior()) {
            $this->replaceEntity();
        } elseif (Import::BEHAVIOR_APPEND === $this->getBehavior()) {
            $this->saveEntity();
        }

        return true;
    }

    /**
     * Deletes newsletter subscriber data from raw data.
     *
     * @return $this
     */
    public function deleteEntity()
    {
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowTitle    = $rowData[self::COL_NAME];
                    $listTitle[] = $rowTitle;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($listTitle) {
            $this->deleteEntityFinish(array_unique($listTitle), self::MP_ATTACHMENT_TABLE_NAME);
        }

        return $this;
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     *
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;

        if (!isset($rowData[self::COL_LABEL]) || empty($rowData[self::COL_LABEL])) {
            $this->addRowError(ValidatorInterface::ERROR_LABEL_IS_EMPTY, $rowNum);

            return false;
        }

        if (!isset($rowData[self::COL_FILE_PATH]) || empty($rowData[self::COL_FILE_PATH])) {
            $this->addRowError(ValidatorInterface::ERROR_FILE_PATH_IS_EMPTY, $rowNum, self::COL_FILE_PATH);
        }

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * @param array $listTitle
     * @param       $table
     *
     * @return bool
     */
    protected function deleteEntityFinish(array $listTitle, $table)
    {
        if ($table && $listTitle) {
            try {
                $this->countItemsDeleted += $this->_connection->delete(
                    $this->_resource->getTableName($table),
                    $this->_connection->quoteInto('name IN (?)', $listTitle)
                );

                return true;
            } catch (Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return $this
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     * @throws FileSystemException
     * @throws LocalizedException
     */
    protected function saveAndReplaceEntity()
    {
        $behavior                = $this->getBehavior();
        $listTitle               = [];
        $storeIds                = [];
        $customerGroup           = [];
        $fileActions             = [];
        $storeList               = $this->_storeManager->getStores();
        $customerGroupCollection = $this->groupFactory->create()->getCollection()->toOptionArray();
        foreach ($storeList as $store) {
            $storeIds[] = $store->getId();
        }
        $storeIds[] = '0';
        foreach ($customerGroupCollection as $group) {
            $customerGroup[] = $group['value'];
        }
        foreach ($this->_fileAction->toOptionArray() as $action) {
            $fileActions[] = (string)$action['value'];
        }

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_LABEL_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $uploadedFile  = $this->_uploadFile('attachment_file')->move($rowData[self::COL_FILE_PATH], false);
                $fileExtension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
                $icons         = $this->_iconList->toOptionArray();
                array_shift($icons);
                $iconPath = '';
                foreach ($icons as $icon) {
                    if ($fileExtension === $icon['label']) {
                        $iconPath = $icon['value'];
                    }
                }
                $dataCustomerGroup      = [];
                $dataStoreIds           = [];
                $isCustomerGroupDefault = true;
                $isStoreIdsDefault      = true;
                if (isset($rowData[self::COL_CUSTOMER_GROUP])) {
                    $dataCustomerGroup = explode(',', $rowData[self::COL_CUSTOMER_GROUP]);
                } else {
                    $isCustomerGroupDefault = false;
                }
                if (isset($rowData[self::COL_STORE_ID])) {
                    $dataStoreIds = explode(',', $rowData[self::COL_STORE_ID]);
                } else {
                    $isStoreIdsDefault = false;
                }
                foreach ($dataCustomerGroup as $item) {
                    if (!in_array($item, $customerGroup)) {
                        $isCustomerGroupDefault = false;
                    }
                }
                foreach ($dataStoreIds as $item) {
                    if (!in_array($item, $storeIds)) {
                        $isStoreIdsDefault = false;
                    }
                }

                if (isset($rowData[self::COL_PRODUCT_SKU]) && !empty($rowData[self::COL_PRODUCT_SKU])) {
                    $conditions = [
                        'type'       => Combine::class,
                        'aggregator' => 'all',
                        'value'      => '1',
                        'conditions' => [
                            [
                                'type'      => Product::class,
                                'operator'  => '()',
                                'value'     => $rowData[self::COL_PRODUCT_SKU],
                                'attribute' => 'sku'
                            ]
                        ]
                    ];
                } else {
                    $conditions = [
                        'type'       => Combine::class,
                        'aggregator' => 'all',
                        'value'      => '1',
                        'new_child'  => ''
                    ];
                }

                $rowTitle                = $rowData[self::COL_NAME];
                $listTitle[]             = $rowTitle;
                $entityList[$rowTitle][] = [
                    self::COL_LABEL          => $rowData[self::COL_LABEL],
                    self::COL_NAME           => $rowData[self::COL_NAME],
                    self::COL_STATUS         => (isset($rowData[self::COL_STATUS])
                        && in_array($rowData[self::COL_STATUS], ['1', '0'], true)) ? $rowData[self::COL_STATUS] : '1',
                    self::COL_STORE_ID       => $isStoreIdsDefault ? $rowData[self::COL_STORE_ID] : '0',
                    self::COL_CUSTOMER_GROUP => $isCustomerGroupDefault ? $rowData[self::COL_CUSTOMER_GROUP] : '0',
                    self::COL_SIZE           => $uploadedFile['size'],
                    self::COL_FILE_PATH      => $uploadedFile['file'],
                    self::COL_FILE_ICON_PATH => $iconPath,
                    self::COL_CUSTOMER_LOGIN => isset($rowData[self::COL_CUSTOMER_LOGIN])
                        ? $rowData[self::COL_CUSTOMER_LOGIN]
                        : '0',
                    self::COL_IS_BUYER       => isset($rowData[self::COL_IS_BUYER]) ?
                        $rowData[self::COL_IS_BUYER] : '0',
                    self::COL_FILE_ACTION    => (isset($rowData[self::COL_FILE_ACTION])
                        && in_array($rowData[self::COL_FILE_ACTION], $fileActions, true))
                        ? $rowData[self::COL_FILE_ACTION]
                        : '1',
                    self::COL_PRIORITY       =>
                        (isset($rowData[self::COL_PRIORITY]) && is_numeric($rowData[self::COL_PRIORITY]))
                            ? $rowData[self::COL_PRIORITY] : '0',
                    self::COL_CREATED_AT     => $this->_dateTime->date(),
                    self::COL_IS_GRID        => 1,
                    self::COL_GROUP          => $rowData[self::COL_GROUP],
                    self::COL_CONDITION      => $this->_helperData->serialize($conditions)
                ];
            }

            if (Import::BEHAVIOR_REPLACE === $behavior) {
                if ($listTitle) {
                    if ($this->deleteEntityFinish(array_unique($listTitle), self::MP_ATTACHMENT_TABLE_NAME)) {
                        $this->saveEntityFinish($entityList, self::MP_ATTACHMENT_TABLE_NAME);
                    }
                }
            } elseif (Import::BEHAVIOR_APPEND === $behavior) {
                $this->saveEntityFinish($entityList, self::MP_ATTACHMENT_TABLE_NAME);
            }
        }

        return $this;
    }

    /**
     * @param $type
     *
     * @return mixed
     * @throws FileSystemException
     * @throws LocalizedException
     */
    protected function _uploadFile($type)
    {
        if (!isset($this->_fileUploader[$type]) || $this->_fileUploader[$type] === null) {
            $fileUploader = $this->_uploaderFactory->create();
            $fileUploader->setAllowRenameFiles(true);
            $fileUploader->setFilesDispersion(true);
            $fileUploader->setAllowCreateFolders(true);

            $dirConfig = DirectoryList::getDefaultConfig();
            $dirAddon  = $dirConfig[DirectoryList::MEDIA][DirectoryList::PATH];
            $DS        = DIRECTORY_SEPARATOR;

            if (empty($this->_parameters[Import::FIELD_NAME_IMG_FILE_DIR])) {
                $tmpPath = $dirAddon . $DS . $this->_mediaDirectory->getRelativePath('import');
            } else {
                $tmpPath = $this->_parameters[Import::FIELD_NAME_IMG_FILE_DIR];
            }
            if (!$fileUploader->setTmpDir($tmpPath)) {
                throw new LocalizedException(
                    __('File directory \'%1\' is not readable.', $tmpPath)
                );
            }
            $destinationDir  = ($type === 'attachment_file')
                ? 'mageplaza/product_attachments/attachment_file'
                : 'mageplaza/product_attachments/file_icons';
            $destinationPath = $dirAddon . $DS . $this->_mediaDirectory->getRelativePath($destinationDir);

            $this->_mediaDirectory->create($destinationPath);
            if (!$fileUploader->setDestDir($destinationPath)) {
                throw new LocalizedException(
                    __('File directory \'%1\' is not writable.', $destinationPath)
                );
            }
            $this->_fileUploader[$type] = $fileUploader;
        }

        return $this->_fileUploader[$type];
    }

    /**
     * @param array $entityData
     * @param       $table
     *
     * @return $this
     * @throws Exception
     */
    protected function saveEntityFinish(array $entityData, $table)
    {
        if ($entityData) {
            $fileModel = $this->_fileModel->create();
            $tableName = $this->_resource->getTableName($table);
            $entityIn  = [];
            foreach ($entityData as $id => $entityRows) {
                foreach ($entityRows as $row) {
                    if ($fileModel->getResource()->isDuplicateFileName($row['name']) != null) {
                        $where                   = [
                            'file_id = ?' => (int)$fileModel->getResource()->isDuplicateFileName($row['name'])
                        ];
                        $this->countItemsUpdated += $this->_connection->update($tableName, $row, $where);
                    } else {
                        $entityIn[] = $row;
                    }
                }
            }
            foreach ($entityIn as $item) {
                $fileModel->setData($item)->save();
                $this->countItemsCreated++;
            }
        }

        return $this;
    }

    /**
     * @return $this
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();

        return $this;
    }
}
