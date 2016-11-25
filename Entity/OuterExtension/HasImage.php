<?php

namespace Librinfo\MediaBundle\Entity\OuterExtension;

use Librinfo\MediaBundle\Entity\Image;

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
    public function setImage(Image $image)
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
