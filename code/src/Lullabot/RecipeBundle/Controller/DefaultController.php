<?php

namespace Lullabot\RecipeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    // Lists recipes.
    public function indexAction()
    {
      return $this->render('LullabotRecipeBundle:Default:index.html.twig', array('name' => 'test'));
    }
}
