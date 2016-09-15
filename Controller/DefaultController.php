<?php

namespace Librinfo\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('LibrinfoMediaBundle:Default:index.html.twig');
    }
}
