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
    $dataDecoded = json_decode(base64_decode($data), NULL, 512, JSON_THROW_ON_ERROR);

    // Load the entity content.
    $entity = $this->entityTypeManager
      ->getStorage($dataDecoded->entity_type)
      ->load($dataDecoded->entity_id);
    $field_name = $dataDecoded->field_name;

    // Use flood service to check if ip has already liked/disliked.
    $flood = \Drupal::flood();
    if ($clicked == 'like') {
      $alreadyClicked = !$flood->isAllowed('iq_blog.like_nid_' . $entity->id(), 1, 86400);
      if (!$alreadyClicked) {
        $entity->$field_name->likes++;
        $entity->save();
        $flood->register('iq_blog.like_nid_' . $entity->id(), 86400);
      }
      $return = $response->addCommand(
        new HtmlCommand('[data-like-dislike-target="like-' . $dataDecoded->entity_id . '"]', '<span>' . $entity->$field_name->likes . '</span>')
      );
    }
    elseif ($clicked == 'dislike') {
      $alreadyClicked = !$flood->isAllowed('iq_blog.dislike_nid_' . $entity->id(), 1, 86400);
      if (!$alreadyClicked) {
        $entity->$field_name->dislikes++;
        $entity->save();
        $flood->register('iq_blog.dislike_nid_' . $entity->id(), 86400);
      }
      $return = $response->addCommand(
        new HtmlCommand('[data-like-dislike-target="dislike-' . $dataDecoded->entity_id . '"]', '<span>' . $entity->$field_name->dislikes . '</span>')
      );
    }

    return $return;
  }

}
