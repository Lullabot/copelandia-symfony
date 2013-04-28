<?php

namespace Lullabot\RecipeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Guzzle\Http\Client;

class DefaultController extends Controller
{
    /**
     * Lists recipes.
     */
    public function indexAction()
    {
      $client = new Client('http://copelandia.local');
      $request = $client->get('/node.json');
      $response = $request->send()->json();

      $recipes = array();
      foreach ($response['list'] as $recipe) {
        $recipes[] = $recipe;
      }

      return $this->render('LullabotRecipeBundle:Default:index.html.twig', array('recipes' => $recipes));
    }

    /**
     * Shows a recipe.
     *
     * @param int $id The id of a recipe.
     */
    public function showAction($id)
    {
      // Obtain recipe details.
      $client = new Client('http://copelandia.local');
      $request = $client->get('/node/' . $id . '.json');
      $recipe = $request->send()->json();

      // Fetch ingredients.
      $ingredients = array();
      foreach ($recipe['field_recipe_ingredients'] as $ingredient) {
        $request = $client->get('/field_collection_item/' . $ingredient['id'] .  '.json');
        $ingredients[] = $request->send()->json();
      }
      $recipe['ingredients'] = $ingredients;

      return $this->render('LullabotRecipeBundle:Default:show.html.twig', array('recipe' => $recipe));
    }
}
