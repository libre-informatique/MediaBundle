<?php

namespace Librinfo\MediaBundle\Entity\OuterExtension;

use Doctrine\Common\Collections\Collection;
use Librinfo\MediaBundle\Entity\File;

trait HasImages
{   
    /**
     * @var Collection
     */
    private $images;

    /**
     * Add image
     *
     * @param object $image
     *
     * @return $this
     */
    public function addImage(File $image)
    {
        $this->images[] = $image;
        return $this;
    }

    /**
     * Remove image
     *
     * @param object $image
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeImage(File $image)
    {
        return $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }
    
    /**
     * Set images
     *
     * @return $this
     */
    public function setImages(array $images)
    {
        $this->images = $images;
        return $this;
    }
    
    public function addLibrinfoFile(File $file)
    {
        $this->addImage($file);
    }
    
    public function removeLibrinfoFile(File $file)
    {
        $this->removeImage($file);
    }
    
    public function getLibrinfoFiles()
    {
        return $this->getImages();
    }
}
