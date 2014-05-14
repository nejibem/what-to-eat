<?php

namespace Nejibem\WhattoeatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Nejibem\WhattoeatBundle\Entity\Ingredient;
use Nejibem\WhattoeatBundle\Entity\Recipe;

class DefaultControllerTest extends WebTestCase
{

    /**
     * @dataProvider usedbydateProvider
     */
    public function test_ingredient_usedbydate( $date, $expected )
    {
        $ingredient = new Ingredient();
        $ingredient->setUsedByDate( $date->format('j/n/Y') );
        $actual = $ingredient->isUsedByDateExpired();

        $this->assertEquals( $expected, $actual );
    }

    public function usedbydateProvider()
    {
        $date1 = new \DateTime('now');
        $date2 = new \DateTime('now');
        $date3 = new \DateTime('now');

        return array(
            array( new \DateTime('yesterday'), true ),
            array( new \DateTime('tomorrow'), false ),
            array( $date3->sub(new \DateInterval('P2D')), true ),
            array( $date1->add(new \DateInterval('P2D')), false ),
            array( $date2->add(new \DateInterval('P3D')), false ),
        );
    }

    public function test_ingredient_closestUsedByDateIngredient_one()
    {
        $date1 = new \DateTime('now');
        $date2 = new \DateTime('now');
        $date3 = new \DateTime('now');
        $date4 = new \DateTime('now');

        $date1->add(new \DateInterval('P2D'));
        $date2->add(new \DateInterval('P1D'));
        $date3->add(new \DateInterval('P3D'));
        $date4->sub(new \DateInterval('P2D'));

        $ingredient1 = new Ingredient();
        $ingredient1->getName('Exp 2 days');
        $ingredient1->setUsedByDate( $date1->format('j/n/Y') );

        $ingredient2 = new Ingredient();
        $ingredient2->getName('Exp 1 days');
        $ingredient2->setUsedByDate( $date2->format('j/n/Y') );

        $ingredient3 = new Ingredient();
        $ingredient3->getName('Exp 3 days');
        $ingredient3->setUsedByDate( $date3->format('j/n/Y') );

        $ingredient4 = new Ingredient();
        $ingredient4->getName('Exp minus 2 days');
        $ingredient4->setUsedByDate( $date4->format('j/n/Y') );

        $availableIngredients = array( $ingredient1,
                                       $ingredient2,
                                       $ingredient3,
                                       $ingredient4 );
        $recipe = new Recipe();
        $recipe->setIngredientsAvailable( $availableIngredients );

        $expected = $ingredient2;
        $actual = $recipe->getclosestUsedByDateIngredient();

        $this->assertEquals( $expected, $actual );
    }

    public function test_ingredient_closestUsedByDateIngredient_two()
    {
        $date1 = new \DateTime('now');
        $date2 = new \DateTime('now');
        $date3 = new \DateTime('now');
        $date4 = new \DateTime('now');

        $date1->add(new \DateInterval('P6D'));
        $date2->add(new \DateInterval('P7D'));
        $date3->add(new \DateInterval('P5D'));
        $date4->sub(new \DateInterval('P1D'));

        $ingredient1 = new Ingredient();
        $ingredient1->getName('Exp 6 days');
        $ingredient1->setUsedByDate( $date1->format('j/n/Y') );

        $ingredient2 = new Ingredient();
        $ingredient2->getName('Exp 7 days');
        $ingredient2->setUsedByDate( $date2->format('j/n/Y') );

        $ingredient3 = new Ingredient();
        $ingredient3->getName('Exp 5 days');
        $ingredient3->setUsedByDate( $date3->format('j/n/Y') );

        $ingredient4 = new Ingredient();
        $ingredient4->getName('Exp minus 1 days');
        $ingredient4->setUsedByDate( $date4->format('j/n/Y') );

        $availableIngredients = array( $ingredient1,
                                       $ingredient2,
                                       $ingredient3,
                                       $ingredient4 );
        $recipe = new Recipe();
        $recipe->setIngredientsAvailable( $availableIngredients );

        $expected = $ingredient3;
        $actual = $recipe->getclosestUsedByDateIngredient();

        $this->assertEquals( $expected, $actual );
    }

    public function test_calcRecipeWithClosestUsedByDateIngredient()
    {
        // recipe 1
        $date1 = new \DateTime('now');
        $date2 = new \DateTime('now');
        $date3 = new \DateTime('now');

        $date1->add(new \DateInterval('P6D'));
        $date2->add(new \DateInterval('P7D'));
        $date3->add(new \DateInterval('P5D'));

        $ingredient1 = new Ingredient();
        $ingredient1->getName('Exp 6 days');
        $ingredient1->setUsedByDate( $date1->format('j/n/Y') );

        $ingredient2 = new Ingredient();
        $ingredient2->getName('Exp 7 days');
        $ingredient2->setUsedByDate( $date2->format('j/n/Y') );

        $ingredient3 = new Ingredient();
        $ingredient3->getName('Exp 5 days');
        $ingredient3->setUsedByDate( $date3->format('j/n/Y') );

        $availableIngredients1 = array( $ingredient1,
                                       $ingredient2,
                                       $ingredient3 );
        $recipe1 = new Recipe();
        $recipe1->setName('recipe one');
        $recipe1->setIngredientsAvailable( $availableIngredients1 );

        // recipe 2
        $date4 = new \DateTime('now');
        $date5 = new \DateTime('now');
        $date6 = new \DateTime('now');

        $date4->add(new \DateInterval('P4D'));
        $date5->add(new \DateInterval('P8D'));
        $date6->add(new \DateInterval('P9D'));

        $ingredient4 = new Ingredient();
        $ingredient4->getName('Exp 4 days');
        $ingredient4->setUsedByDate( $date4->format('j/n/Y') );

        $ingredient5 = new Ingredient();
        $ingredient5->getName('Exp 8 days');
        $ingredient5->setUsedByDate( $date5->format('j/n/Y') );

        $ingredient6 = new Ingredient();
        $ingredient6->getName('Exp 9 days');
        $ingredient6->setUsedByDate( $date6->format('j/n/Y') );

        $availableIngredients2 = array( $ingredient4,
                                        $ingredient5,
                                        $ingredient6 );
        $recipe2 = new Recipe();
        $recipe2->setName('recipe two');
        $recipe2->setIngredientsAvailable( $availableIngredients2 );

        // assert
        $recipies = array( $recipe1,
                           $recipe2 );
        $expected = $recipe2;
        $actual = Recipe::calcRecipeWithClosestUsedByDateIngredient($recipies);

        $this->assertEquals( 'recipe two', $expected->getName() );
        $this->assertEquals( $expected, $actual );

        // recipe 3
        $date7 = new \DateTime('now');
        $date8 = new \DateTime('now');
        $date9 = new \DateTime('now');

        $date7->add(new \DateInterval('P3D'));
        $date8->add(new \DateInterval('P1D'));
        $date9->add(new \DateInterval('P2D'));

        $ingredient7 = new Ingredient();
        $ingredient7->getName('Exp 3 days');
        $ingredient7->setUsedByDate( $date7->format('j/n/Y') );

        $ingredient8 = new Ingredient();
        $ingredient8->getName('Exp 1 days');
        $ingredient8->setUsedByDate( $date8->format('j/n/Y') );

        $ingredient9 = new Ingredient();
        $ingredient9->getName('Exp 2 days');
        $ingredient9->setUsedByDate( $date9->format('j/n/Y') );

        $availableIngredients3 = array( $ingredient7,
                                        $ingredient8,
                                        $ingredient9 );
        $recipe3 = new Recipe();
        $recipe3->setName('recipe three');
        $recipe3->setIngredientsAvailable( $availableIngredients3 );

        // assert
        $recipies[] = $recipe3;

        $expected = $recipe3;
        $actual = Recipe::calcRecipeWithClosestUsedByDateIngredient($recipies);

        $this->assertEquals( 'recipe three', $expected->getName() );
        $this->assertEquals( $expected, $actual );
    }


    public function hasAllAvailableIngredientsProvider()
    {
        $ingredient1 = new Ingredient();
        $ingredient1->setName('bread');
        $ingredient1->setQuantity(2);

        $ingredient2 = new Ingredient();
        $ingredient2->setName('cheese');
        $ingredient2->setQuantity(1);

        $date1 = new \DateTime('now');
        $date2 = new \DateTime('now');

        $date1->add(new \DateInterval('P6D'));
        $date2->add(new \DateInterval('P7D'));

        $availableIngredient1 = new Ingredient();
        $availableIngredient1->setName('bread');
        $availableIngredient1->setQuantity(2);
        $availableIngredient1->setUsedByDate( $date1->format('j/n/Y') );

        $availableIngredient2 = new Ingredient();
        $availableIngredient2->setName('cheese');
        $availableIngredient2->setQuantity(1);
        $availableIngredient2->setUsedByDate( $date2->format('j/n/Y') );

        return array(
            array( $ingredient1, $ingredient2, $availableIngredient1, $availableIngredient2 )
        );
    }

    /**
     * @dataProvider hasAllAvailableIngredientsProvider
     */
    public function test_recipe_hasAllAvailableIngredients_success( $ingredient1, $ingredient2, $availableIngredient1, $availableIngredient2 )
    {
        $recipe1 = new Recipe();
        $recipe1->addIngredient( $ingredient1 );
        $recipe1->addIngredient( $ingredient2 );
        $recipe1->addIngredientAvailable( $availableIngredient1 );
        $recipe1->addIngredientAvailable( $availableIngredient2 );

        $this->assertTrue( $recipe1->hasAllAvailableIngredients() );
    }

    /**
     * @dataProvider hasAllAvailableIngredientsProvider
     */
    public function test_recipe_hasAllAvailableIngredients_failure_missingIngredient( $ingredient1, $ingredient2, $availableIngredient1, $availableIngredient2 )
    {
        $recipe1 = new Recipe();
        $recipe1->addIngredient( $ingredient1 );
        $recipe1->addIngredient( $ingredient2 );
        $recipe1->addIngredientAvailable( $availableIngredient1 );

        $this->assertFalse( $recipe1->hasAllAvailableIngredients() );
    }

    /**
     * @dataProvider hasAllAvailableIngredientsProvider
     */
    public function test_recipe_hasAllAvailableIngredients_failure_usedByDateExpired( $ingredient1, $ingredient2, $availableIngredient1, $availableIngredient2 )
    {
        $date1 = new \DateTime('yesterday');
        $availableIngredient1->setUsedByDate( $date1->format('j/n/Y') );

        $recipe1 = new Recipe();
        $recipe1->addIngredient( $ingredient1 );
        $recipe1->addIngredient( $ingredient2 );
        $recipe1->addIngredientAvailable( $availableIngredient1 );
        $recipe1->addIngredientAvailable( $availableIngredient2 );

        $this->assertFalse( $recipe1->hasAllAvailableIngredients() );
    }

    /**
     * @dataProvider hasAllAvailableIngredientsProvider
     */
    public function test_recipe_hasAllAvailableIngredients_failure_quantityInsufficient( $ingredient1, $ingredient2, $availableIngredient1, $availableIngredient2 )
    {
        $availableIngredient1->setQuantity(1);

        $recipe1 = new Recipe();
        $recipe1->addIngredient( $ingredient1 );
        $recipe1->addIngredient( $ingredient2 );
        $recipe1->addIngredientAvailable( $availableIngredient1 );
        $recipe1->addIngredientAvailable( $availableIngredient2 );

        $this->assertFalse( $recipe1->hasAllAvailableIngredients() );
    }



    public function calcWhatToEatProvider()
    {
        // recipe 1
        $ingredient1 = new Ingredient();
        $ingredient1->setName('bread');
        $ingredient1->setQuantity(2);

        $ingredient2 = new Ingredient();
        $ingredient2->setName('cheese');
        $ingredient2->setQuantity(2);

        $date1 = new \DateTime('now');
        $date2 = new \DateTime('now');

        $date1->add(new \DateInterval('P6D'));
        $date2->add(new \DateInterval('P7D'));

        $availableIngredient1 = new Ingredient();
        $availableIngredient1->setName('bread');
        $availableIngredient1->setQuantity(2);
        $availableIngredient1->setUsedByDate( $date1->format('j/n/Y') );

        $availableIngredient2 = new Ingredient();
        $availableIngredient2->setName('cheese');
        $availableIngredient2->setQuantity(2);
        $availableIngredient2->setUsedByDate( $date2->format('j/n/Y') );

        $recipe1 = new Recipe();
        $recipe1->setName('grilled cheese on toast');
        $recipe1->addIngredient( $ingredient1 );
        $recipe1->addIngredient( $ingredient2 );


        // recipe 2
        $ingredient3 = new Ingredient();
        $ingredient3->setName('bread');
        $ingredient3->setQuantity(2);

        $ingredient4 = new Ingredient();
        $ingredient4->setName('salad');
        $ingredient4->setQuantity(100);

        $date3 = new \DateTime('now');
        $date4 = new \DateTime('now');

        $date3->add(new \DateInterval('P3D'));
        $date4->add(new \DateInterval('P4D'));

        $availableIngredient3 = new Ingredient();
        $availableIngredient3->setName('bread');
        $availableIngredient3->setQuantity(2);
        $availableIngredient3->setUsedByDate( $date3->format('j/n/Y') );

        $availableIngredient4 = new Ingredient();
        $availableIngredient4->setName('salad');
        $availableIngredient4->setQuantity(100);
        $availableIngredient4->setUsedByDate( $date4->format('j/n/Y') );

        $recipe2 = new Recipe();
        $recipe2->setName('salad sandwich');
        $recipe2->addIngredient( $ingredient3 );
        $recipe2->addIngredient( $ingredient4 );

        return array(
            array( $recipe1, $availableIngredient1, $availableIngredient2, $recipe2, $availableIngredient3, $availableIngredient4 )
        );
    }

    /**
     * @dataProvider calcWhatToEatProvider
     */
    public function test_recipe_calcWhatToEat_success( $recipe1, $availableIngredient1, $availableIngredient2, $recipe2, $availableIngredient3, $availableIngredient4 )
    {
        $recipe1->addIngredientAvailable( $availableIngredient1 );
        $recipe1->addIngredientAvailable( $availableIngredient2 );
        $recipe2->addIngredientAvailable( $availableIngredient3 );
        $recipe2->addIngredientAvailable( $availableIngredient4 );
        $recipies = array( $recipe1, $recipe2 );

        $expected = Recipe::calcWhatToEat($recipies);
        $actual = $recipe2;

        $this->assertEquals( 'salad sandwich', $expected->getName() );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @dataProvider calcWhatToEatProvider
     */
    public function test_recipe_calcWhatToEat_onlyOneRecipe( $recipe1, $availableIngredient1, $availableIngredient2, $recipe2, $availableIngredient3, $availableIngredient4 )
    {
        $recipe1->addIngredientAvailable( $availableIngredient1 );
        $recipe1->addIngredientAvailable( $availableIngredient2 );
        $recipies = array( $recipe1 );

        $expected = Recipe::calcWhatToEat($recipies);
        $actual = $recipe1;

        $this->assertEquals( 'grilled cheese on toast', $expected->getName() );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @dataProvider calcWhatToEatProvider
     */
    public function test_recipe_calcWhatToEat_success_alternateRecipeViaExpiryDate( $recipe1, $availableIngredient1, $availableIngredient2, $recipe2, $availableIngredient3, $availableIngredient4 )
    {
        $date1 = new \DateTime('tomorrow');
        $availableIngredient1->setUsedByDate( $date1->format('j/n/Y') );

        $recipe1->addIngredientAvailable( $availableIngredient1 );
        $recipe1->addIngredientAvailable( $availableIngredient2 );
        $recipe2->addIngredientAvailable( $availableIngredient3 );
        $recipe2->addIngredientAvailable( $availableIngredient4 );
        $recipies = array( $recipe1, $recipe2 );

        $expected = Recipe::calcWhatToEat($recipies);
        $actual = $recipe1;

        $this->assertEquals( 'grilled cheese on toast', $expected->getName() );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @dataProvider calcWhatToEatProvider
     */
    public function test_recipe_calcWhatToEat_failure_noRecipeExpiredFood( $recipe1, $availableIngredient1, $availableIngredient2, $recipe2, $availableIngredient3, $availableIngredient4 )
    {
        $date1 = new \DateTime('yesterday');
        $availableIngredient1->setUsedByDate( $date1->format('j/n/Y') );

        $date2 = new \DateTime('yesterday');
        $availableIngredient3->setUsedByDate( $date1->format('j/n/Y') );

        $recipe1->addIngredientAvailable( $availableIngredient1 );
        $recipe1->addIngredientAvailable( $availableIngredient2 );
        $recipe2->addIngredientAvailable( $availableIngredient3 );
        $recipe2->addIngredientAvailable( $availableIngredient4 );
        $recipies = array( $recipe1, $recipe2 );

        $expected = new Recipe();
        $expected->setName('Order Takeaway!');
        $expected->setRecommended(true);
        $actual = Recipe::calcWhatToEat($recipies);

        $this->assertEquals( $expected, $actual );
    }


}
