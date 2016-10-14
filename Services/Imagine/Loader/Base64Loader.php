<?php
namespace Librinfo\MediaBundle\Services\Imagine\Loader;

use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Liip\ImagineBundle\Model\Binary;

class Base64Loader implements LoaderInterface
{
    /**
     * @param File $image
     *
     * @return BinaryInterface
     */
    public function find($image)
    {
        // return binary instance with data
        return new Binary($image->getBase64File(), $image->getMimeType());
    }
}