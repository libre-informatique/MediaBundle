<?php

namespace Librinfo\MediaBundle\Entity\OuterExtension;

use Librinfo\MediaBundle\Entity\File;

trait HasImage
{
    /**
     * @var Image
     */
    private $image;

    /**
     * Set image
     *
     * @param File $image
     *
     * @return self
     */
    public function setImage(File $image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    public function setLibrinfoFile(File $file)
    {
        $this->setImage($file);
    }

    public function getLibrinfoFile()
    {
        return $this->getImage();
    }

}
