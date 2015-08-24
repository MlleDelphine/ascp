<?php

namespace ServiceCivique\Bundle\ImporterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception as FsException;
use Symfony\Component\Console\Helper\ProgressBar;

class ImportResumesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:update:resumes')
            ->setDescription('Import and rename resumes to uploads/cv')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $webRoot = $this->getContainer()->get('kernel')->getRootDir() . '/../web';

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $params = array(
            'batchSize'       => 5000,
            'originalPath'    => $webRoot . '/uploads/webform/',
            'destinationPath' => $webRoot . '/uploads/cv/',
        );

        // Import default profile resume
        $profileRepository = $doctrine->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Profile');
        $this->importResume($profileRepository, $params, $output, $em, 'profile');

        // Import default profile resume
        $applicationRepository = $doctrine->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Application');
        $this->importResume($applicationRepository, $params, $output, $em, 'application');
    }

    protected function importResume($repository, $params, $output, $em, $table)
    {
        $batchSize       = $params['batchSize'];
        $originalPath    = $params['originalPath'];
        $destinationPath = $params['destinationPath'];
        $itemCount = $repository->getAllWithCVCount();
        $fs = new Filesystem();

        $conn = $em->getConnection();
        $conn->getConfiguration()->setSQLLogger(null);

        $progress = new ProgressBar($output, $itemCount);
        $progress->start();
        $progress->setFormat('debug');
        $progress->setRedrawFrequency(100);

        // Batching
        for ($i = 0; $i < $itemCount; $i += $batchSize) {
            $items = $repository->getAllWithCV($i, $batchSize);

            $conn->beginTransaction();

            foreach ($items as $item) {
                try {
                    $destinationFilename = $this->generateResumeUrl($item);
                    $originalFilepath    = $originalPath . $item->getCv();
                    $destinationFilepath = $destinationPath . $destinationFilename;

                    // if destination file does not exist
                    // then move it form orginal path to new path
                    if (!$fs->exists($destinationFilepath)) {
                        $fs->copy($originalFilepath, $destinationFilepath);
                        $fs->remove($originalFilepath);
                    }

                    if ($item->getPath() !== $destinationFilename) {
                        $conn->update($table, array('path' => $destinationFilename), array('id' => $item->getId()));
                    }

                    unset($item);

                } catch (FsException\FileNotFoundException $e) {
                    $progress->setMessage(sprintf('<error>File %s not found</error> : %s', $item->getCv(), $e->getMessage()));
                } catch (FsException\IOException $e) {
                    $progress->setMessage(sprintf('<error>An error occured while moving the resume %s</error> : %s', $item->getCv(), $e->getMessage()));
                }

                $progress->advance();
            }

            $conn->commit();

            $items = null;
            unset($items);

            $em->clear();
        }

        $progress->finish();
    }

    protected function generateResumeUrl($item)
    {
        $dir       = pathinfo($item->getCv(), PATHINFO_DIRNAME);
        $extension = pathinfo($item->getCv(), PATHINFO_EXTENSION);

        $fullname = $item->getUser()->getFullName();

        if (trim($fullname) == '') {
            $fullname = $item->getUser()->getUsername();
        }

        return \Gedmo\Sluggable\Util\Urlizer::urlize(
            sprintf(
                '%s-%s',
                substr(sha1($item->getCV()), 0, 6),
                $fullname
            )
        ) . '.' . $extension;
    }
}
