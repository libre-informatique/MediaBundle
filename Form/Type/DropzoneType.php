<?php

namespace Librinfo\MediaBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Librinfo\CoreBundle\Form\AbstractType as BaseAbstractType;

/**
 * File upload form type
 */
class DropzoneType extends BaseAbstractType
{
    public function getParent()
    {
        return 'form';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        
    }

    public function getBlockPrefix()
    {
        return 'librinfo_dropzone';
    }

}
