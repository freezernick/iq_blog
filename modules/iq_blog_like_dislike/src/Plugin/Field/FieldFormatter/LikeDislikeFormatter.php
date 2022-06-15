<?php

namespace Drupal\iq_blog_like_dislike\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Plugin implementation of the 'iq_blog_like_dislike_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "iq_blog_like_dislike_formatter",
 *   label = @Translation("iq Blog - Like/Disklike"),
 *   field_types = {
 *     "iq_blog_like_dislike"
 *   }
 * )
 */
class LikeDislikeFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs an ImageFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings settings.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   The request stack.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, AccountInterface $current_user, RequestStack $request) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->currentUser = $current_user;
    $this->requestStack = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('current_user'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = 'en') {
    $entity = $items->getEntity();
    $elements = [];

    // Data to be passed in the url.
    $initial_data = [
      'entity_type' => $entity->getEntityTypeId(),
      'entity_id' => $entity->id(),
      'field_name' => $items->getFieldDefinition()->getName(),
    ];
    foreach ($items as $delta => $item) {
      $initial_data['likes'] = $items[$delta]->likes;
      $initial_data['dislikes'] = $items[$delta]->dislikes;
    }
    $data = base64_encode(json_encode($initial_data));

    $like_url = Url::fromRoute(
      'iq_blog_like_dislike.manager', ['clicked' => 'like', 'data' => $data]
    )->toString();
    $dislike_url = Url::fromRoute(
      'iq_blog_like_dislike.manager', ['clicked' => 'dislike', 'data' => $data]
    )->toString();

    // If user is anonymous, then append the destination back url.
    $user = $this->currentUser->id();
    $destination = '';
    if ($user == 0) {
      $destination = '?like-dislike-redirect=' . $this->requestStack->getCurrentRequest()->getUri();
    }

    $elements[] = [
      '#theme' => 'iq_like_dislike',
      '#likes' => array_key_exists('likes', $initial_data) ? $initial_data['likes'] : 0,
      '#dislikes' => array_key_exists('dislikes', $initial_data) ? $initial_data['dislikes'] : 0,
      '#like_url' => $like_url . $destination,
      '#dislike_url' => $dislike_url . $destination,
      '#entity_id' => $initial_data['entity_id'],
    ];

    $elements['#attached']['library'][] = 'core/drupal.ajax';
    $elements['#attached']['library'][] = 'iq_blog_like_dislike/blog-post-like-dislike';

    $elements[0]['#theme'] = 'iq_like_dislike';
    $elements[0]['#hide_dislike'] = $this->getSetting('hide_dislike');
    $elements[0]['#entity_id'] = $items->getEntity()->id();
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'hide_dislike' => 'boolean',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['hide_dislike'] = [
      '#title' => $this->t('Hide dislike'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('hide_dislike'),
    ];

    return $form;
  }

}
