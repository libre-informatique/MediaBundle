<?php

namespace Librinfo\MediaBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Librinfo\DoctrineBundle\Entity\Traits\BaseEntity;
use Librinfo\OuterExtensionBundle\Entity\Traits\OuterExtensible;
use Librinfo\DoctrineBundle\Entity\Traits\Jsonable;
use AppBundle\Entity\Extension\FileExtension;

/**
 * File
 */
class File implements \JsonSerializable
{
    use BaseEntity,
        OuterExtensible,
        FileExtension,
        Jsonable;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var float
     */
    private $size;

    /**
     * @var UploadedFile file
     */
    private $file;
    
    /**
     * @var string
     */
    private $tempId;
    
    /**
     * @var object
     */
    private $parent;
    
    /**
     * 
     */
    public function __construct()
    {
        $this->initOuterExtendedClasses();
    }

    /**
     * 
     * @param UploadedFile $file
     * @return \Librinfo\MediaBundle\Entity\File
     */
    public function setFile( $file = null)
    {
        if ($file instanceof UploadedFile)
            $this->file = base64_encode(file_get_contents($file));
        else
            $this->file = $file;
            
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getFile()
    {
        return base64_decode($this->file);
    }

    /**
     * 
     * @return type
     */
    public function getBase64File()
    {
        return $this->file;
    }
    
    /**
     * Set parent
     *
     * @param string $parent
     *
     * @return File
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     *
     * @return File
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set size
     *
     * @param float $size
     *
     * @return File
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return float
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set tempId
     *
     * @param string $tempId
     *
     * @return EmailAttachment
     */
    public function setTempId($tempId)
    {
        $this->tempId = $tempId;

        return $this;
    }

    /**
     * Get tempId
     *
     * @return string
     */
    public function getTempId()
    {
        return $this->tempId;
    }
    
    public function __clone()
    {
        $this->id = null;
        $this->initOuterExtendedClasses();
    }
}
