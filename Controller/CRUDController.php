<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Librinfo\MediaBundle\Controller;

use Blast\CoreBundle\Controller\CRUDController as BaseCRUDController;

/**
 * Class CRUDController.
 *
 * @author  Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class CRUDController extends BaseCRUDController
{    
    /**
     *
     * @var EntityManager $manager
     */
    protected $manager;
    
    /**
     * Overrides LibrinfoCore CRUDController and adds uploaded files management
     *
     * @param Email $object
     * @return Response
     */
    public function createAction($object = Null)
    {
        $request = $this->getRequest();
        $this->manager = $this->getDoctrine()->getManager();
        // the key used to lookup the template
        $templateKey = 'edit';

        $this->admin->checkAccess('create');

        $class = new \ReflectionClass($this->admin->hasActiveSubClass() ? $this->admin->getActiveSubClass() : $this->admin->getClass());

        if ($class->isAbstract())
        {
            return $this->render(
                            'SonataAdminBundle:CRUD:select_subclass.html.twig', array(
                        'base_template' => $this->getBaseTemplate(),
                        'admin' => $this->admin,
                        'action' => 'create',
                            ), null, $request
            );
        }

        $object = $object ? $object : $this->admin->getNewInstance();
        
        $preResponse = $this->preCreate($request, $object);
        if ($preResponse !== null)
        {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        $form->handleRequest($request);
        
        $this->handleFiles($object, $request->get('temp_id'));
        
        if ($form->isSubmitted())
        {
            //TODO: remove this check for 3.0
            if (method_exists($this->admin, 'preValidate'))
            {
                $this->admin->preValidate($object);
            }
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode($request) || $this->isPreviewApproved($request)))
            {
                $this->admin->checkAccess('create', $object);

                try {
                    $object = $this->admin->create($object);

                    if ($this->isXmlHttpRequest())
                    {
                        return $this->renderJson(array(
                                    'result' => 'ok',
                                    'objectId' => $this->admin->getNormalizedIdentifier($object),
                                        ), 200, array());
                    }

                    $this->addFlash(
                            'sonata_flash_success', $this->admin->trans(
                                    'flash_create_success', array('%name%' => $this->escapeHtml($this->admin->toString($object))), 'SonataAdminBundle'
                            )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($object);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid)
            {
                if (!$this->isXmlHttpRequest())
                {
                    $this->addFlash(
                            'sonata_flash_error', $this->admin->trans(
                                    'flash_create_error', array('%name%' => $this->escapeHtml($this->admin->toString($object))), 'SonataAdminBundle'
                            )
                    );
                }
            } elseif ($this->isPreviewRequested())
            {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
                    'action' => 'create',
                    'form' => $view,
                    'object' => $object,
                        ), null);
    }
    
    /**
     * Overrides SonataAdminBundle CRUDController
     *
     * @param type $id
     * @return type
     * @throws type
     */
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        $this->manager = $this->getDoctrine()->getManager();
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object)
        {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->checkAccess('edit', $object);

        $preResponse = $this->preEdit($request, $object);
        if ($preResponse !== null)
        {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        /** @var $form Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        $form->handleRequest($request);
        
        $this->handleFiles($object, $request->get('temp_id'));
        
        if ($form->isSubmitted())
        {
            //TODO: remove this check for 3.0
            if (method_exists($this->admin, 'preValidate'))
            {
                $this->admin->preValidate($object);
            }
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved()))
            {
                try {
                    $object = $this->admin->update($object);
         
                    if ($this->isXmlHttpRequest())
                    {
                        return $this->renderJson(array(
                                    'result' => 'ok',
                                    'objectId' => $this->admin->getNormalizedIdentifier($object),
                                    'objectName' => $this->escapeHtml($this->admin->toString($object)),
                                        ), 200, array());
                    }

                    $this->addFlash(
                            'sonata_flash_success', $this->admin->trans(
                                    'flash_edit_success', array('%name%' => $this->escapeHtml($this->admin->toString($object))), 'SonataAdminBundle'
                            )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($object);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                } catch (LockException $e) {
                    $this->addFlash('sonata_flash_error', $this->admin->trans('flash_lock_error', array(
                                '%name%' => $this->escapeHtml($this->admin->toString($object)),
                                '%link_start%' => '<a href="' . $this->admin->generateObjectUrl('edit', $object) . '">',
                                '%link_end%' => '</a>',
                                    ), 'SonataAdminBundle'));
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid)
            {
                if (!$this->isXmlHttpRequest())
                {
                    $this->addFlash(
                            'sonata_flash_error', $this->admin->trans(
                                    'flash_edit_error', array('%name%' => $this->escapeHtml($this->admin->toString($object))), 'SonataAdminBundle'
                            )
                    );
                }
            } elseif ($this->isPreviewRequested())
            {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
                    'action' => 'edit',
                    'form' => $view,
                    'object' => $object,
                        ), null);
    }
    
    /**
     * Binds the uploaded file to its owner on creation
     *
     * @param Object $object
     * @param String $tempId
     */
    protected function handleFiles($object, $tempId)
    {
        $rc = new \ReflectionClass($object);
        $setter = 'set' . $rc->getShortName();
        
        $repo = $this->manager->getRepository('LibrinfoMediaBundle:File');
        $files = $repo->findBy(array(
            'tempId' => $tempId,
            strtolower($rc->getShortName()) => null
            ));

        foreach ($files as $file)
            $file->$setter($object);
    }
 
}