<?php

namespace BowlingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BowlingBundle:Default:index.html.twig');
    }


}
