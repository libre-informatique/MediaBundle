<?php

namespace Librinfo\MediaBundle\Imagine\Loader;

use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Liip\ImagineBundle\Model\Binary;
use Doctrine\ORM\EntityManager;
use Librinfo\MediaBundle\Imagine\PathResolver\PathResolverInterface;

class DbLoader implements LoaderInterface
{

    /**
     *
     * @var EntityManager
     */
    private $em;
    
    /**
     * @var PathResolverInterface 
     */
    private $pathResolver;
    
    /**
     * @var mixed
     */
    private $liipImagineDriver;

    public function __construct($pathResolver, $liipImagineDriver)
    {
        $this->pathResolver = $pathResolver;
        $this->liipImagineDriver = $liipImagineDriver;
    }

    /**
     * @param mixed $path
     *
     * @return BinaryInterface
     */
    public function find($path)
    {
        $data = $this->pathResolver->resolvePath($path);
        $mime = $this->pathResolver->resolveMime($path);

        $binary = new Binary($data, $mime,str_replace('image/','',$mime));
        
        return $binary;
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
