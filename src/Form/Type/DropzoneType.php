<?php

namespace Librinfo\MediaBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Blast\CoreBundle\Form\AbstractType as BaseAbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

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
        $resolver->setDefault('mediaContext', 'default');
        $resolver->setDefault('dropzoneTemplate', 'LibrinfoMediaBundle:dropzone:dropzone_template.mustache.twig');

        $resolver->setAllowedTypes('mediaContext', 'string');
        $resolver->setAllowedTypes('dropzoneTemplate', 'string');
    }

    public function getBlockPrefix()
    {
        return 'librinfo_dropzone';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array_merge($view->vars, array(
            'mediaContext'     => $options['mediaContext'],
            'dropzoneTemplate' => $options['dropzoneTemplate']
        ));
    }

}
