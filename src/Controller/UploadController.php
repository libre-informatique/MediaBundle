<?php

namespace Librinfo\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Librinfo\MediaBundle\Entity\File;
use Symfony\Component\EventDispatcher\GenericEvent;
use Librinfo\MediaBundle\Events\UploadControllerEventListener;

class UploadController extends Controller
{

    /**
     * Upload
     * 
     * @param Request $request
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $file = $request->files->get('file');

        $new = new File();
        $new->setFile($file);
        $new->setMimeType($file->getMimeType());
        $new->setName($file->getClientOriginalName());
        $new->setSize($file->getClientSize());
        $new->setOwned(false);

        $manager->persist($new);
        $manager->flush();

        return new Response($new->getId(), 200);
    }

    /**
     * Removal
     * 
     * @param String $fileId
     * @return Response
     */
    public function removeAction($fileId = null)
    {
        if (!$fileId) {
            return new Response("Please provide a file id", 500);
        }

        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('LibrinfoMediaBundle:File');

        $file = $repo->findOneBy([
            'id'    => $fileId,
            'owned' => false
        ]);

        $manager->remove($file);
        $manager->flush();

        return new Response($file->getName() . " removed successfully", 200);
    }

    /**
     * Retrieves
     * 
     * @param Request $request
     * @return Response files converted to json array
     */
    public function loadAction(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository('LibrinfoMediaBundle:File');
        $files = [];

        foreach ($request->get('load_files') as $key => $id) {
            $file = null;

            $dispatcher = $this->get('event_dispatcher');

            $event = new GenericEvent(
                [
                'request' => $request,
                'context' => ['key' => $key, 'id' => $id, 'file' => $file]
                ], [
                'file'  => $file,
                'files' => $files
                ]
            );
            $dispatcher->dispatch(UploadControllerEventListener::PRE_GET_ENTITY, $event);

            $file = $event->getArgument('file');

            if ($file) {
                $file->setFile($file->getBase64File());
                $files[] = $file;
            }

            $event = new GenericEvent(
                [
                'request' => $request,
                'context' => [
                    'key'  => $key,
                    'id'   => $id,
                    'file' => $file
                ]
                ], [
                'file'  => $file,
                'files' => $files
                ]
            );
            $dispatcher->dispatch(UploadControllerEventListener::POST_GET_ENTITY, $event);
        }

        return new JsonResponse($files, 200);
    }

}
