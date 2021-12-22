<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Setup;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Tax\Model\TaxClass\Source\Product as ProductTaxClassSource;
use Magento\Tax\Model\TaxClass\Source\Customer as CustomerTaxClassSource;
use I95DevConnect\MessageQueue\Api\LoggerInterface;
use Magento\Tax\Api\Data\TaxRateInterface;
use Magento\Tax\Api\TaxRateRepositoryInterface;

/**
 * Class for handling data adding during module install
 */
class InstallSchema implements InstallSchemaInterface
{
    const NULLABLE = 'nullable';
    const CONNECTORRULE = 'ConnectorRule';
    /**
     * @var Magento\Framework\App\State $appState
     */
    private $appState;

    /**
     * @var ProductTaxClassSource
     */
    private $productTaxClassSource;

    /**
     * @var CustomerTaxClassSource
     */
    private $customerTaxClassSource;

    /**
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     *
     * @var \Magento\Tax\Model\Calculation\RateFactory
     */
    public $rateFactory;

    /**
     *
     * @var \Magento\Tax\Model\Calculation\RuleFactory
     */
    public $ruleFactory;

    public $taxRateInterface;
    public $taxRateRepository;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\State $appState
     * @param LoggerInterface $logger
     * @param \Magento\Tax\Model\Calculation\RateFactory $rateFactory
     * @param \Magento\Tax\Model\Calculation\RuleFactory $ruleFactory
     * @param ProductTaxClassSource $productTaxClassSource
     * @param CustomerTaxClassSource $customerTaxClassSource
     * @param TaxRateInterface $taxRateInterface
     * @param TaxRateRepositoryInterface $taxRateRepository
     * @author Debashis S. Gopal
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        LoggerInterface $logger,
        \Magento\Tax\Model\Calculation\RateFactory $rateFactory,
        \Magento\Tax\Model\Calculation\RuleFactory $ruleFactory,
        ProductTaxClassSource $productTaxClassSource,
        CustomerTaxClassSource $customerTaxClassSource,
        TaxRateInterface $taxRateInterface,
        TaxRateRepositoryInterface $taxRateRepository
    ) {
        $this->appState = $appState;
        $this->logger = $logger;
        $this->rateFactory = $rateFactory;
        $this->ruleFactory = $ruleFactory;
        $this->productTaxClassSource = $productTaxClassSource;
        $this->customerTaxClassSource = $customerTaxClassSource;
        $this->taxRateInterface = $taxRateInterface;
        $this->taxRateRepository = $taxRateRepository;
    }

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        // Get i95dev_netterms_erp_report table
        $taxBusPostingGroupTable = $installer->getTable('i95dev_tax_busposting_group');
        // Check if the table already exists
        if (!$installer->getConnection()->isTableExists($taxBusPostingGroupTable)) {
            $this->createGroupTable(
                $installer,
                'i95dev_tax_busposting_group',
                'I95dev Tax Bus Posting Group'
            );
        }

        $taxProductPostingGroupTable = $installer->getTable('i95dev_tax_productposting_group');
        if (!$installer->getConnection()->isTableExists($taxProductPostingGroupTable)) {
            $this->createGroupTable(
                $installer,
                'i95dev_tax_productposting_group',
                'I95dev Tax Product Bus Posting Group'
            );
        }

        $taxPostingSetupTable = $installer->getTable('i95dev_tax_postingsetup');
        if (!$installer->getConnection()->isTableExists($taxPostingSetupTable)) {
            $taxPostingSetup = $installer->getConnection()
                    ->newTable($installer->getTable('i95dev_tax_postingsetup'))
                    ->addColumn(
                        'id',
                        Table::TYPE_BIGINT,
                        null,
                        ['identity' => true, self::NULLABLE => false, 'primary' => true],
                        'ID'
                    )
                    ->addColumn(
                        'tax_busposting_group_code',
                        Table::TYPE_TEXT,
                        null,
                        [self::NULLABLE => false],
                        'Tax Bus Posting Group Code'
                    )
                    ->addColumn(
                        'tax_productposting_group_code',
                        Table::TYPE_TEXT,
                        null,
                        [self::NULLABLE => false],
                        'Tax product Posting Group Code'
                    )
                    ->addColumn('tax_percentage', Table::TYPE_TEXT, null, [self::NULLABLE => false], 'Tax Percentage')
                    ->addColumn('created_date', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Created Date')
                    ->addColumn('updated_date', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Updated Date')
                    ->setComment('I95dev Tax Posting Setup');
            $installer->getConnection()->createTable($taxPostingSetup);
        }
        $this->createTaxrule($installer);
        $installer->endSetup();
    }

    public function createGroupTable($installer, $tableName, $tableCmt)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable($tableName))
            ->addColumn(
                'id',
                Table::TYPE_BIGINT,
                null,
                ['identity' => true, self::NULLABLE => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'code',
                Table::TYPE_TEXT,
                null,
                [self::NULLABLE => false],
                'Code'
            )
            ->addColumn('description', Table::TYPE_TEXT, null, [self::NULLABLE => false], 'Description')
            ->addColumn('created_date', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Created Date')
            ->addColumn('updated_date', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Updated Date')
            ->setComment($tableCmt);
        $installer->getConnection()->createTable($table);
    }
    /**
     * Create default Tax rule on module installation
     * @author Debashis S. Gopal. Updated code. Setting Customer tax class id and
     * Product tax class id with dynamic valueinstead of static value.
     */
    public function createTaxrule($installer)
    {
        try {
            $this->appState->setAreaCode('frontend');

            $taxCalculationRate1 = $this->rateFactory->create();
            if ($taxCalculationRate1->loadByCode(self::CONNECTORRULE)->getId()) {
                return;
            }
            $productTaxClassID = $customerTaxClassID = 0;
            $productTaxClass = $this->productTaxClassSource->getAllOptions();
            foreach ($productTaxClass as $taxClass) {
                if ($taxClass['label'] === 'Taxable Goods') {
                    $productTaxClassID = $taxClass['value'];
                }
            }
            $customerTaxClass = $this->customerTaxClassSource->getAllOptions();
            foreach ($customerTaxClass as $taxClass) {
                if ($taxClass['label'] === 'Retail Customer') {
                    $customerTaxClassID =  $taxClass['value'];
                }
            }

            $magTaxRateInterface = $this->taxRateInterface;
            $magTaxRateInterface->setTaxCountryId('US');
            $magTaxRateInterface->setZipIsRange(0);
            $magTaxRateInterface->setTaxPostcode('*');
            $magTaxRateInterface->setCode(self::CONNECTORRULE);
            $magTaxRateInterface->setRate(1);
            try {
                $taxRate = $this->taxRateRepository->save($magTaxRateInterface);
            } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                $this->logger->createLog(
                    __METHOD__,
                    $exception->getMessage(),
                    LoggerInterface::I95EXC,
                    LoggerInterface::CRITICAL
                );
            }

            $fixtureTaxRule1 = $this->ruleFactory->create();
            $fixtureTaxRule1->setTaxCalculationRateId($taxRate->getId());
            $fixtureTaxRule1->setCode(self::CONNECTORRULE);
            $fixtureTaxRule1->setPriority(0);
            $fixtureTaxRule1->setCustomerTaxClassIds([$customerTaxClassID]);
            $fixtureTaxRule1->setProductTaxClassIds([$productTaxClassID]);
            $fixtureTaxRule1->setTaxRateIds([$taxRate->getId()]);
            $fixtureTaxRule1->save();
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->logger->createLog(
                __METHOD__,
                $exception->getMessage(),
                LoggerInterface::I95EXC,
                LoggerInterface::CRITICAL
            );
        }
    }
}
