<?php

namespace Nejibem\WhattoeatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Zend\Http\Client;
use Nejibem\WhattoeatBundle\Entity\Ingredient;
use Nejibem\WhattoeatBundle\Entity\Recipe;

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

    public function whattoeatAction(Request $request)
    {
        $ingredients = null;
        $form = $this->createFormBuilder()
            ->add('ingredients_file', 'file', array('label' => 'Ingredients File to Submit'))
            ->add('add', 'submit')
            ->getForm();

        $form->handleRequest($request);
        if( $request->getMethod() == 'POST' )
        {
            if ($form->isValid())
            {
                $file = $form->get('ingredients_file');
                $fileData = $file->getData()->openFile('r');
                $ingredients = $this->parseIngredients($fileData);
            }
        }

        $recipies = $this->fetchRecipies();
        $this->determineRecommendedRecipe( $ingredients, $recipies );

        $params = array( 'form'        => $form->createView(),
                         'ingredients' => $ingredients,
                         'recipies'    => $recipies,
                        );

        return $this->render('NejibemWhattoeatBundle:Default:whattoeat.html.twig', $params );
    }


    public function parseIngredients( $file )
    {
        $ingredients = [];
        while (!$file->eof()) {
            $arr =  $file->fgetcsv();
            $ingredient = new Ingredient();
            $ingredient->setName($arr[0]);
            $ingredient->setQuantity($arr[1]);
            $ingredient->setUnit($arr[2]);
            $ingredient->setUsedByDate($arr[3]);

            $ingredients[] = $ingredient;
        }
        return $ingredients;
    }

    public function fetchRecipies()
    {
        $client = new Client('http://symfony.dev/recipes', array(
            'adapter' => 'Zend\Http\Client\Adapter\Curl'
        ));
        $response = $client->send();

        $recipieObjs = json_decode( $response->getContent() );
        $recipies = array();

        foreach( $recipieObjs->data as $recipeObj )
        {
            $recipe = new Recipe();
            $recipe->setName( $recipeObj->name );
            foreach( $recipeObj->ingredients as $ingredientObj )
            {
                $ingredient = new Ingredient();
                $ingredient->setName( $ingredientObj->item );
                $ingredient->getQuantity( $ingredientObj->amount );
                $ingredient->setUnit( $ingredientObj->unit );
                $recipe->addIngredient( $ingredient );
            }
            $recipies[] = $recipe;
        }

        return $recipies;
    }

    public function determineRecommendedRecipe( $ingredients, $recipies )
    {

    }


}



