<?php

namespace Librinfo\MediaBundle\Imagine\PathResolver;

use Doctrine\ORM\EntityManager;
use Librinfo\MediaBundle\Entity\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultResolver implements PathResolverInterface
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var File 
     */
    protected $cacheFile = null;

    public function resolvePath($id)
    {
        try {
            $repo = $this->em->getRepository('LibrinfoMediaBundle:File');

            if (!$this->cacheFile) {
                /** @var $this->cacheFile File */
                $this->cacheFile = $repo->find($id);
            }

            return $this->cacheFile->getFile();
        } catch (\Exception $e) {
            throw new NotFoundHttpException(sprintf('File « %s » was not found', $id));
        }
    }

    public function resolveMime($id)
    {
        try {
            $this->resolvePath($id);

            return $this->cacheFile->getMimeType();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * setEm(EntityManager $em)
     * 
     * @param EntityManager $em
     * @return $this
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;
        return $this;
    }

}
