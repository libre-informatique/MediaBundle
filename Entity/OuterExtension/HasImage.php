<?php

namespace Librinfo\MediaBundle\Entity\OuterExtension;

trait HasImage
{
    /**
     * @var Image
     */
    private $image;

    /**
     * Set image
     *
     * @param object $image
     *
     * @return this
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

}