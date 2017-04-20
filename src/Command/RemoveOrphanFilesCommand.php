<?php

namespace Librinfo\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Send Emails from the spool.
 */
class RemoveOrphanFilesCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('librinfo:remove:orphan-files')
            ->setDescription('Deletes all files that are not linked to any entity from the database')
        ;
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = 0;
        
        $manager = $this->getContainer()->get('doctrine')->getManager();
        
        $orphans = $manager->getRepository('LibrinfoMediaBundle:File')->findBy(['owned' => false]);

        foreach( $orphans as $file )
        {
            $count ++;
            $manager->remove($file);
        }
        
        $manager->flush();
        
        $output->write(sprintf('%s orphan files where removed', $count), true);
    }

}
