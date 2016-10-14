<?php

namespace Librinfo\MediaBundle\Entity\Traits;

use Doctrine\Common\Collections\Collection;

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
     * @return this
     */
    public function addImage($image)
    {
        die('addImage');
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
    public function removeImage($image)
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setImages($images)
    {
        die('setImages');
        $this->images = $images;
        return $this;
    }

}