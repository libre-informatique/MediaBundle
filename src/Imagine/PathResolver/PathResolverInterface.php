<?php

namespace Librinfo\MediaBundle\Imagine\PathResolver;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

interface PathResolverInterface {
    /**
     * @param string $path
     * @return string
     * @throws NotFoundHttpException
     */
    public function resolvePath($path);
    
    /**
     * @param string $path
     * @return string
     * @throws NotFoundHttpException
     */
    public function resolveMime($path);
}

