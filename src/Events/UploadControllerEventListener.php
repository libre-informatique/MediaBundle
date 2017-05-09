<?php

namespace Librinfo\MediaBundle\Events;

use Symfony\Component\EventDispatcher\GenericEvent;
use Doctrine\ORM\EntityManager;

class UploadControllerEventListener 
{

    const PRE_GET_ENTITY = 'librinfo.events.media.load.preGetEntity';
    const POST_GET_ENTITY = 'librinfo.events.media.load.postGetEntity';
    
    /**
     * @var EntityManager
     */
    protected $em;
    
    public function preGetEntity(GenericEvent $event) {
        
        $repo = $this->em->getRepository('LibrinfoMediaBundle:File');
        
        $file = $repo->find($event->getSubject()['context']['id']);
        
        $event->setArgument('file', $file);
    }
    
    public function postGetEntity(GenericEvent $event) {
        
    }
    public function setEm(EntityManager $em)
    {
        $this->em = $em;
        return $this;
    }

}
