<?php

namespace Drupal\iq_blog_like_dislike\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class LikeDislikeController hanldes liking via Ajax.
 *
 * @package Drupal\iq_blog_like_dislike\Controller
 */
class LikeDislikeController extends ControllerBase {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs an LinkClickCountController object.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   The request stack.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(RequestStack $request, EntityTypeManagerInterface $entity_type_manager, AccountInterface $account, RendererInterface $renderer) {
    $this->requestStack = $request;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $account;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('renderer')
    );
  }

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
