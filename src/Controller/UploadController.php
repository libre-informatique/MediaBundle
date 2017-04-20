<?php

namespace Librinfo\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Librinfo\MediaBundle\Entity\File;

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
    public function removeAction($fileId)
    {
        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('LibrinfoMediaBundle:File');

        $file = $repo->findOneBy([
            'id'    =>$fileId,
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
        
        foreach( $request->get('load_files') as $key => $id )
        {
            $file = $repo->find($id);
            
            if ( $file )
            {
                $file->setFile($file->getBase64File());
                $files[] = $file;
            }
        }
            
        return new JsonResponse($files, 200);
    }
}
