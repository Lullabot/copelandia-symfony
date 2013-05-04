<?php

namespace Lullabot\DrupalUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Guzzle\Http\Client;

class DefaultController extends Controller
{
  /**
   * Registers a user in the system.
   */
  public function registerAction(Request $request)
  {
    $form = $this->createFormBuilder()
      ->add('email', 'email')
      ->getForm();

    if ($request->isMethod('POST')) {
      $form->bind($request);

      if ($form->isValid()) {
        // Register the user.
        $data = $form->getData();
        $client = new Client('http://copelandia.local');
        $request = $client->post('api/v1/user/register')
          ->addPostFields(array('email' => $data['email']));
        $response = $request->send()->json();

        // Evaluate the response. If OK we have a reset password link. If KO, an error message.
        if ($response['status'] == 'OK') {
          // Send an email to verify and activate account.
          // @todo this should be a link to this site that notifies Drupal that
          // the account has been verified.
          $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('send@example.com')
            ->setTo($data['email'])
            ->setBody(
              $this->renderView(
                'LullabotDrupalUserBundle:Default:welcome.html.twig',
                  array('url' => $response['message'])
                )
            );
          $this->get('mailer')->send($message);

          // Show a message and redirect to the homepage.
          $this->get('session')->getFlashBag()->add('notice', 'Please check your email to complete registration.');
          return $this->redirect($this->generateUrl('homepage'));
        }
        else {
          // There was an error. Just rebuild the form and show the message.
          $form->get('email')->addError(new FormError($response['message']));
        }
      }
    }
    return $this->render('LullabotDrupalUserBundle:Default:register.html.twig', array(
      'form' => $form->createView(),
    ));
  }
}
