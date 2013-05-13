<?php

namespace Lullabot\DrupalUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
  /**
   * Registers a user in the system.
   */
  public function registerAction(Request $request)
  {
    // Build registration form.
    $form = $this->createFormBuilder()
      ->add('mail', 'email')
      ->add('pass', 'password')
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
        $request = $client->post('/api/user', null, array(
          'name' => preg_replace('/[^a-z0-9]/', '', $data['mail']),
          'mail' => $data['mail'],
          'pass' => $data['pass'],
          'status' => 1,
        ));
        try {
          // Create the user and redirect to the homepage.
          $response = $request->send()->json();
          $this->get('session')->getFlashBag()->add('notice', 'Registered');
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
    return $this->render('LullabotDrupalUserBundle:Default:register.html.twig', array(
      'form' => $form->createView(),
    ));
  }

  /**
   * Authenticates a user.
   */
  public function loginAction()
  {
      $request = $this->getRequest();
      $session = $request->getSession();

      // get the login error if there is one
      if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
          $error = $request->attributes->get(
              SecurityContext::AUTHENTICATION_ERROR
          );
      } else {
          $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
          $session->remove(SecurityContext::AUTHENTICATION_ERROR);
      }

      return $this->render(
          'LullabotDrupalUserBundle:Default:login.html.twig',
          array(
              // last username entered by the user
              'last_username' => $session->get(SecurityContext::LAST_USERNAME),
              'error'         => $error,
          )
      );
  }
}
