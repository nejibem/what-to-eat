<?php

namespace Nejibem\WhattoeatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('NejibemWhattoeatBundle:Default:index.html.twig');
    }

    public function recipesAction()
    {
        $recipesArray = [ 'data' =>
                            [
                                [
                                    "name" =>  "grilled cheese on toast",
                                    "ingredients" =>  [
                                        [ "item" => "bread", "amount" => "2", "unit" => "slices"],
                                        [ "item" => "cheese", "amount" => "2", "unit" => "slices"]
                                    ]
                                ]
                                ,
                                [
                                    "name" =>  "salad sandwich",
                                    "ingredients" =>  [
                                        [ "item" => "bread", "amount" => "2", "unit" => "slices"],
                                        [ "item" => "mixed salad", "amount" => "100", "unit" => "grams"]
                                    ]
                                ]
                            ]
                        ];

        return new JsonResponse($recipesArray);
    }

}



