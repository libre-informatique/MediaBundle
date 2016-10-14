<?php

namespace Librinfo\MediaBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class FileParentListener implements LoggerAwareInterface, EventSubscriber
{
    /**
     * @var Logger
     */
    private $logger;
    
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'loadClassMetadata',
        ];
    }

    /**
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();
        $className = 'Librinfo\MediaBundle\Entity\File';
        $this->logger->debug("[MediaBundle] Entering FileParentListener for « loadClassMetadata » event");
        //$parentName = strtolower($metadata->getReflectionClass()->getShortName());
        foreach( $metadata->getAssociationMappings() as $association)
            if( $association['targetEntity'] == $className )     
            {dump($association);
               $eventArgs->getEntityManager()->getClassMetadata($className)->mapManyToMany([            
                    'targetEntity' => $metadata->getName(),
                    'inversed_by'  => $association['fieldName'],
                    'fieldName'    => 'parents',
                    'join_table'   => [
                        'name' => 'librinfo_media_file',
                        'joinColumns' => ['file_id' => ['referencedColumnName' => 'id']],
                        'inverseJoinColumns' => ['parent_id' => ['referencedColumnName' => 'id']],
                    ]
                ]);
            }
        $this->logger->debug("[MediaBundle] Added Parent mapping metadata to File Entity", ['class' => $metadata->getName()]);
    }
    
    // mapping with Organism entity (many-to-many owning side)
//        $metadata->mapManyToMany([
//            'targetEntity' => 'Librinfo\CRMBundle\Entity\Organism',
//            'fieldName'    => 'organisms',
//            'inversedBy'   => 'emailMessages',
//            'joinTable'    => [
//                'name'               => 'librinfo_email_email__organism',
//                'joinColumns'        => ['email_id' => ['referencedColumnName' => 'id']],
//                'inverseJoinColumns' => ['organism_id'    => ['referencedColumnName' => 'id']],
//            ]
//        ]);

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

}
