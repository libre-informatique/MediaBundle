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

        $tempId = $request->get('temp_id');
        $file = $request->files->get('file');

        $new = new File();
        $new->setTempId($tempId);
        $new->setFile($file);
        $new->setMimeType($file->getMimeType());
        $new->setName($file->getClientOriginalName());
        $new->setSize($file->getClientSize());
        $new->setOwned(false);

        $manager->persist($new);
        $manager->flush();

        return new Response("Ok", 200);
    }

    /**
     * Removal
     * 
     * @param String $fileName
     * @param String $fileSize
     * @param String $tempId
     * @return Response
     */
    public function removeAction($fileName, $fileSize, $tempId)
    {

        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('LibrinfoMediaBundle:File');

        $file = $repo->findOneBy(array(
            'name' => $fileName,
            'size' => $fileSize,
            'tempId' => $tempId
        ));

        $manager->remove($file);
        $manager->flush();

        return new Response($fileName . " removed successfully", 200);
    }

    /**
     * Retrieves
     * 
     * @param String $parentId
     * @return Response files converted to json array
     */
    public function loadAction($ownerId, $ownerType)
    {
        $repo = $this->getDoctrine()->getRepository('LibrinfoMediaBundle:File');

        $files = $repo->findBy([$ownerType => $ownerId]);
        
        foreach($files as $key => $file)
            $file->setFile($file->getBase64File());

        return new JsonResponse($files, 200);
    }
    
    public function updateAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('LibrinfoMediaBundle:File');
        $request = $this->getRequest();
        $newTempId = $request->get('new_temp_id');

        $files = $repo->findBy(array(
            'tempId' => $request->get('temp_id'),
            'owned' => false
            ));
        
        foreach($files as $file)
        {
            $file->setTempId($newTempId);
            $manager->persist($file);
        }
        $manager->flush();
        
        return new Response($newTempId, 200);
    }

}
