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

    // Use flood service to check if ip has already liked/disliked.
    $flood = \Drupal::flood();
    if ($clicked == 'like') {
      $already_clicked = !$flood->isAllowed('iq_blog.like_nid_' . $entity_data->id(), 1, 86400);
      if (!$already_clicked) {
        $entity_data->$field_name->likes++;
        $entity_data->save();
        $flood->register('iq_blog.like_nid_' . $entity_data->id(), 86400);
      }
      $return = $response->addCommand(
        new HtmlCommand('[data-like-dislike-target="like-' . $decode_data->entity_id . '"]', '<span>' . $entity_data->$field_name->likes . '</span>')
      );
    }
    elseif ($clicked == 'dislike') {
      $already_clicked = !$flood->isAllowed('iq_blog.dislikenid_' . $entity_data->id(), 1, 86400);
      if (!$already_clicked) {
        $entity_data->$field_name->dislikes--;
        $entity_data->save();
        $flood->register('iq_blog.dislike' . $entity_data->id(), 86400);
      }
      $return = $response->addCommand(
        new HtmlCommand('[data-like-dislike-target="dislike-' . $decode_data->entity_id . '"]', '<span>' . $entity_data->$field_name->dislikes . '</span>')
      );
    }

    return $return;
  }

}
