<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2021 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ErrorData
 */

namespace I95DevConnect\ErrorData\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Cron extends Command
{
    protected function configure()
    {
        $this->setName('i95dev:error-notification');
        $this->setDescription('i95Dev Error Notifications');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $dateFormat = 'Y-m-d H:i:s';
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $directory = $objectManager->get(\Magento\Framework\Filesystem\DirectoryList::class);
            $fileSystem = $objectManager->create(\Magento\Framework\Filesystem\Driver\File::class);
            $state = $objectManager->get(\Magento\Framework\App\State::class);
            $state->setAreaCode('frontend');
            $rootPath = $directory->getRoot();

            $filename = $rootPath . "/var/log/i95dev-error-notification-cron.log";
            $fp = $fileSystem->fileOpen($filename, "a");
            $fileSystem->fileWrite($fp, "Cron Started " . date($dateFormat) . "\n");
            $fileSystem->fileClose($fp);
            try {
                $i95devErrorNotification = $objectManager->create("\I95DevConnect\ErrorData\Controller\Index\Report");
                $i95devErrorNotification->execute();
                $output->writeln('success');
                $fp = $fileSystem->fileOpen($filename, "a");
                $fileSystem->fileWrite($fp, "Cron End " . date($dateFormat) . "\n");
                $fileSystem->fileClose($fp);
            } catch (\Magento\Framework\Exception\LocalizedException $ex) {
                $fp = $fileSystem->fileOpen($filename, "a");
                $fileSystem->fileWrite($fp, $ex->getMessage() . "\n");
                $fileSystem->fileWrite($fp, "Cron End " . date($dateFormat) . "\n");
                $fileSystem->fileClose($fp);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $output->writeln($ex->getMessage());
        }
    }
}
