<?php
/**
 * Created by PhpStorm.
 * User: benm
 * Date: 13/05/2014
 * Time: 8:49 PM
 */

namespace Nejibem\WhattoeatBundle\Entity;


class Recipe {

    private $name;
    private $ingredients;
    private $ingredientsAvailable;
    private $recommended;

    function __construct()
    {
        $this->ingredients = array();
        $this->ingredientsAvailable = array();
        $this->recommended = false;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Ingredient
     */
    public function addIngredient( Ingredient $ingredient )
    {
        $this->ingredients[] = $ingredient;
    }

    /**
     * @param mixed $ingredients
     */
    public function setIngredients($ingredients)
    {
        $this->ingredients = $ingredients;
    }

    /**
     * @return mixed
     */
    public function getIngredients()
    {
        return $this->ingredients;
    }

    /**
     * @param mixed $recommended
     */
    public function setRecommended($recommended)
    {
        $this->recommended = $recommended;
    }

    /**
     * @return mixed
     */
    public function isRecommended()
    {
        return $this->recommended;
    }

    /**
     * @param Ingredient
     */
    public function addIngredientAvailable( Ingredient $ingredientAvailable )
    {
        $this->ingredientsAvailable[] = $ingredientAvailable;
    }

    /**
     * @param mixed $ingredientsAvailable
     */
    public function setIngredientsAvailable($ingredientsAvailable)
    {
        $this->ingredientsAvailable = $ingredientsAvailable;
    }

    /**
     * @return mixed
     */
    public function getIngredientsAvailable()
    {
        return $this->ingredientsAvailable;
    }

    public function getclosestUsedByDateIngredient()
    {
        $this->usedByIntervals = array();
        foreach( $this->ingredientsAvailable as $ingredient )
        {
            $interval = $ingredient->getUsedByDateInterval();
            if( $interval > 0 )
            {
                $this->usedByIntervals[$interval] = $ingredient;
            }
        }
        ksort($this->usedByIntervals);
        return array_shift($this->usedByIntervals);
    }

    public function hasAvailableIngredient( $ingredient )
    {
        foreach( $this->ingredientsAvailable as $available )
        {
            if( $ingredient->getName() == $available->getName() &&
                $ingredient->getQuantity() <= $available->getQuantity() &&
                $available->isUsedByDateExpired() == false
            )
            {
                return true;
            }
        }
        return false;
    }

    public function hasAllAvailableIngredients()
    {
        foreach( $this->ingredients as $ingredient )
        {
            if( $this->hasAvailableIngredient($ingredient) == false )
            {
                return false;
            }
        }
        return true;
    }

    public static function calcRecipeWithClosestUsedByDateIngredient( $recipies )
    {
        $usedByIntervals = array();
        foreach( $recipies as $recipe )
        {
            $interval = $recipe->getclosestUsedByDateIngredient()->getUsedByDateInterval();
            if( $interval > 0 )
            {
                $usedByIntervals[$interval] = $recipe;
            }
        }
        ksort($usedByIntervals);
        return array_shift($usedByIntervals);
    }

    public static function calcWhatToEat( $recipies )
    {
        $maybeEat = array();
        foreach( $recipies as $recipe )
        {
            if( $recipe->hasAllAvailableIngredients() )
            {
                $maybeEat[] = $recipe;
            }
        }

        if( count($maybeEat) > 1 )
        {
            $recipe = recipe::calcRecipeWithClosestUsedByDateIngredient( $maybeEat );
            $recipe->setRecommended(true);
            return $recipe;

        }
        elseif( count($maybeEat) == 1 )
        {
            $recipe = $maybeEat[0];
            $recipe->setRecommended(true);
            return $recipe;
        }
        else
        {
            return false;
        }
    }
} 