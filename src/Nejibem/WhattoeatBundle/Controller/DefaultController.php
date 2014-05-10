<?php

namespace Nejibem\WhattoeatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('NejibemWhattoeatBundle:Default:index.html.twig', array('name' => $name));
    }
}
