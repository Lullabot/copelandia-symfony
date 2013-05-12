<?php

namespace Lullabot\RecipeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    /**
     * Lists recipes.
     */
    public function indexAction()
    {
      $client = new Client('http://copelandia.lulladev.com');
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
      $client = new Client('http://copelandia.lulladev.com');
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

    /**
     * Submits a recipe
     *
     */
    public function createAction(Request $request)
    {
      // Build recipe form.
      $form = $this->createFormBuilder()
        ->add('title', 'text')
        ->add('body', 'textarea')
        ->add('prep_time', 'integer')
        ->add('cook_time', 'integer')
        ->add('servings', 'integer')
        ->add('instructions', 'textarea')
        ->add('source', 'text')
        ->getForm();

      // Process form submission.
      if ($request->isMethod('POST')) {
        $form->bind($request);
        if ($form->isValid()) {
          // Create, authenticate and populate the request.
          $data = $form->getData();
          $client = new Client('http://copelandia.local');
          $client->addSubscriber(new OauthPlugin(array(
            'consumer_key'  => '4UJQ3xW6e2E9aLbkMXQcUG772rE3FTVz',
            'consumer_secret' => 'fWT4py9n9PLzQ5STeZCPiPhopfszAPq4',
          )));
          $request = $client->post('/api/node', null, array(
            'title' => $data['title'],
            'body' => array('und' => array(array('value' => $data['body']))),
            'field_recipe_prep_time' => array('und' => array(array('value' => $data['prep_time']))),
            'field_recipe_cook_time' => array('und' => array(array('value' => $data['cook_time']))),
            'field_recipe_servings' => array('und' => array(array('value' => $data['servings']))),
            'field_recipe_instructions' => array('und' => array(array('value' => $data['instructions']))),
            'field_recipe_source' => array('und' => array(array('value' => $data['source']))),
            'type' => 'recipe'
          ));
          try {
            // Create the recipe and redirect to the homepage.
            $response = $request->send()->json();
            $this->get('session')->getFlashBag()->add('notice', 'Submitted');
            return $this->redirect($this->generateUrl('homepage'));
          } catch (\Exception $e) {
            // Add the errors to the form.
            $errors = $e->getResponse()->json();
            // The name field in Drupal is not needed here since we use the mail.
            unset($errors['form_errors']['name']);
            foreach ($errors['form_errors'] as $field_name => $message) {
              $form->get($field_name)->addError(new FormError($message));
            }
          }
        }
      }

      // Render the form.
      return $this->render('LullabotRecipeBundle:Default:create.html.twig', array(
        'form' => $form->createView(),
      ));

    }
}
