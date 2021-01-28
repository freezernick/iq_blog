<?php

namespace Drupal\iq_blog_like_dislike\Controller;

use Drupal\like_dislike\Controller\LikeDislikeController as LikeDislikeControllerBase;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class LikeDislikeController.
 *
 * @package Drupal\iq_blog_like_dislike\Controller
 */
class LikeDislikeController extends LikeDislikeControllerBase {

  /**
   * {@inheritdoc}
   */
  public function handler($clicked, $data) {




    $return = '';
    $response = new AjaxResponse();


    // Decode the url data.
    $decode_data = json_decode(base64_decode($data));

    // Load the entity content.
    $entity_data = $this->entityTypeManager
      ->getStorage($decode_data->entity_type)
      ->load($decode_data->entity_id);
    $field_name = $decode_data->field_name;

    // Get the users who already clicked on this particular content.
    $users = json_decode($entity_data->$field_name->clicked_by);
    if ($users == NULL) {
      $users = new \stdClass();
      $users->default = 'default';
    }

    // add flood service to check if ip has already liked/disliked
    // Update content, based on like/dislike.
    $already_clicked = key_exists($user, array_keys((array) $users));
    $already_clicked = FALSE;
    if ($clicked == 'like') {
      if (!$already_clicked) {
        $entity_data->$field_name->likes++;
        // $users->$user = 'like';
      }
      else {
        return $this->like_dislike_status($response);
      }
      $return = $response->addCommand(
        new HtmlCommand('[data-like-dislike-target="like-' . $decode_data->entity_id . '"]', '<span>' . $entity_data->$field_name->likes . '</span>')
      );
    }
    elseif ($clicked == 'dislike') {
      if (!$already_clicked) {
        $entity_data->$field_name->dislikes--;
        // $users->$user = "dislike";
      }
      else {
        return $this->like_dislike_status($response);
      }
      $return = $response->addCommand(
        new HtmlCommand('[data-like-dislike-target="dislike-' . $decode_data->entity_id . '"]', '<span>' . $entity_data->$field_name->dislikes . '</span>')
      );
    }
    // $entity_data->$field_name->clicked_by = json_encode($users);
    $entity_data->save();
    return $return;
  }

}
