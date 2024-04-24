<?php

namespace Drupal\static_generator\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class StaticPublishController extends ControllerBase {

  public function publish() {
    // Implement your logic to publish to Cloudflare here.
    // This is a placeholder for the actual implementation.

    // Provide feedback to the user.
    $this->messenger()->addStatus($this->t('Successfully published to Cloudflare.'));
    return new Response('', Response::HTTP_OK);
  }
}
