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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp;

use Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\Exception\CouldNotDownloadException;
use Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\File\Downloader as FileDownloader;
use Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\File\Info as FileInfo;
use Magento\Framework\Archive;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\OneStepCheckout\Model\GeoIp\UrlProvider as GeoIpUrlProvider;

/**
 * Class DatabaseDownloader
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp
 */
class DatabaseDownloader
{
    const MAX_ARCHIVE_NESTING_LEVEL = 2;

    /**
     * @var FileDownloader
     */
    private $fileDownloader;

    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @var Archive
     */
    private $archive;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var GeoIpUrlProvider
     */
    private $geoIpUrlProvider;

    /**
     * @param FileDownloader $fileDownloader
     * @param FileInfo $fileInfo
     * @param Archive $archive
     * @param DriverPool $driverPool
     * @param TimezoneInterface $localeDate
     * @param GeoIpUrlProvider $geoIpUrlProvider
     */
    public function __construct(
        FileDownloader $fileDownloader,
        FileInfo $fileInfo,
        Archive $archive,
        DriverPool $driverPool,
        TimezoneInterface $localeDate,
        GeoIpUrlProvider $geoIpUrlProvider
    ) {
        $this->fileDownloader = $fileDownloader;
        $this->fileInfo = $fileInfo;
        $this->archive = $archive;
        $this->driver = $driverPool->getDriver(DriverPool::FILE);
        $this->localeDate = $localeDate;
        $this->geoIpUrlProvider = $geoIpUrlProvider;
    }

    /**
     * Download and unpack database from given path
     *
     * @param string $dbFileName
     * @param string $archiveName
     * @return array
     * @throws CouldNotDownloadException
     * @throws RuntimeException
     * phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
     */
    public function downloadAndUnpack($dbFileName, $archiveName)
    {
        $path = $this->geoIpUrlProvider->getDownloadUrl();
        $dbPath = null;

        try {
            $downloadedPath = $this->download($path, $archiveName);
            $isArchive = $this->archive->isArchive($downloadedPath);
            if ($isArchive) {
                $workPaths = [];
                $unpackedPath = $this->unpackArchive($downloadedPath, $workPaths);
                if ($unpackedPath) {
                    $findResult = $this->findFile($unpackedPath, $dbFileName);
                    if ($findResult) {
                        $destinationPath = $this->fileInfo->getAbsolutePath($dbFileName);
                        $this->driver->copy($findResult, $destinationPath);
                        $dbPath = $destinationPath;
                    }
                }
                foreach ($workPaths as $wPath) {
                    $this->deleteFileOrDirectory($wPath);
                }
            } elseif ($this->isDbFile($downloadedPath, $dbFileName)) {
                $dbPath = $downloadedPath;
            }
        } catch (\Exception $e) {
            throw new CouldNotDownloadException(__('Unable to download database.'));
        }

        if ($dbPath) {
            $fileName = pathinfo($dbPath, PATHINFO_BASENAME);
            $modifiedTm = $this->fileInfo->getModificationTimestamp($fileName);
            return [
                'file_name' => $fileName,
                'modified_at' => $this->localeDate->formatDateTime(
                    (new \DateTime())->setTimestamp($modifiedTm)
                )
            ];
        } else {
            throw new RuntimeException(__('Unable to find %1 in %2.', $dbFileName, $path));
        }
    }

    /**
     * Download file
     *
     * @param string $path
     * @param string $archiveName
     * @return string
     * @throws FileSystemException
     */
    private function download($path, $archiveName)
    {
        $this->fileDownloader->download($path, $archiveName);
        return $this->fileInfo->getAbsolutePath($archiveName);
    }

    /**
     * Unpack archive
     *
     * @param string $path
     * @param array $workPaths
     * @return null|string
     * phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
     */
    private function unpackArchive($path, &$workPaths)
    {
        $result = null;
        $workPaths = [];

        $archivePath = $path;
        $workDir = $this->fileInfo->getAbsolutePath();
        $isArchive = $this->archive->isArchive($path);
        $nestingLevel = 1;
        while ($isArchive && $nestingLevel <= self::MAX_ARCHIVE_NESTING_LEVEL) {
            if (!$this->driver->isDirectory($workDir)) {
                $this->driver->createDirectory($workDir);
            }
            $result = $this->archive->unpack($archivePath, $workDir);
            $isArchive = $this->archive->isArchive($result);
            $this->collectWorkPaths($workPaths, [$archivePath, $result]);

            if ($isArchive) {
                $nestingLevel++;
                $workDir = $workDir . '/' . pathinfo($result, PATHINFO_FILENAME);
                $archivePath = $result;
                $this->collectWorkPaths($workPaths, $workDir);
            }
        }
        return $result;
    }

    /**
     * Collect work paths array
     *
     * @param array $workPaths
     * @param array|string $paths
     * @return void
     */
    private function collectWorkPaths(&$workPaths, $paths)
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }
        foreach ($paths as $path) {
            if (!in_array($path, $workPaths)) {
                $workPaths[] = $path;
            }
        }
    }

    /**
     * Delete file or directory
     *
     * @param string $path
     * @return void
     */
    private function deleteFileOrDirectory($path)
    {
        if ($this->driver->isExists($path)) {
            if ($this->driver->isFile($path)) {
                $this->driver->deleteFile($path);
            } else {
                $this->driver->deleteDirectory($path);
            }
        }
    }

    /**
     * Find file in given path and nesting subdirectories
     *
     * @param string $path
     * @param string $fileName
     * @return bool
     */
    private function findFile($path, $fileName)
    {
        if ($this->driver->isDirectory($path)) {
            $dirContent = $this->driver->readDirectoryRecursively($path);
            foreach ($dirContent as $contentPath) {
                if ($this->isDbFile($contentPath, $fileName)) {
                    return $contentPath;
                }
            }
        } elseif ($this->isDbFile($path, $fileName)) {
            return $path;
        }
        return false;
    }

    /**
     * Check if filename is database file
     *
     * @param string $path
     * @param string $fileName
     * @return bool
     * phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
     */
    private function isDbFile($path, $fileName)
    {
        return $this->driver->isFile($path)
            && $fileName == pathinfo($path, PATHINFO_BASENAME);
    }
}
