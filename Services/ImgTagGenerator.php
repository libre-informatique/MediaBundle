<?php

namespace Librinfo\MediaBundle\Services;

use Librinfo\MediaBundle\Entity\File;

/**
 * Description of ImgTagGenerator
 *
 * @author romain
 */
class ImgTagGenerator
{
    public function generateTag($file)
    {
        if(!$this->isImage($file))
            return;
        
        $alt = explode('.', $file->getName())[0];

        $tag = '<img src="data:' . $file->getMimeType() . ';base64,' . $file->getBase64File() . '" alt="' . $alt . '" />';

        return $tag;
    }
    
    /**
     * Checks if the file is an image according to its mimetype
     * 
     * @param File $file
     * @return boolean
     */
    private function isImage($file)
    {
        if ($file && preg_match('!^image\/!', $file->getMimeType()) === 1)
        {
            return true;
        }
        return false;
    }
}
