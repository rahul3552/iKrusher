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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\FileSystem\Export;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Class Csv
 *
 * @package Aheadworks\QuickOrder\Model\FileSystem\Export
 */
class Csv
{
    /**
     * File name
     */
    const FILENAME = 'aw_qo_sample.csv';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    /**
     * Create csv file with content
     *
     * @param array $content
     * @param string $baseDir
     * @return array
     * @throws FileSystemException
     */
    public function createFile(
        $content,
        $baseDir = DirectoryList::VAR_DIR
    ) {
        $directory = $this->filesystem->getDirectoryWrite($baseDir);
        $directory->create('export');
        $file = 'export/' . self::FILENAME;
        $stream = $directory->openFile($file, 'w+');
        $stream->lock();
        foreach ($content as $line) {
            $stream->writeCsv($line);
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true
        ];
    }
}
