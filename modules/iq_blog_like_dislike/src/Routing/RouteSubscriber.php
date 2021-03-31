<?php

namespace Drupal\iq_blog_like_dislike\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('like_dislike.manager')) {
      $route->setDefaults([
        '_controller' => '\Drupal\iq_blog_like_dislike\Controller\LikeDislikeController::handler',
      ]);
      // Remove CSRF token generation, since this would require a user session to work properly.
      $requirements = $route->getRequirements();
      unset($requirements['_csrf_token']);
      $route->setRequirements($requirements);      
    }
  }

}
