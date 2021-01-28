<?php

namespace Drupal\iq_blog_like_dislike\Plugin\Field\FieldFormatter;

use Drupal\like_dislike\Plugin\Field\FieldFormatter\LikeDislikeFormatter as LikeDislikeFormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'like_dislike_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "iq_blog_like_formatter",
 *   label = @Translation("iq Blog - Like/Disklike"),
 *   field_types = {
 *     "like_dislike"
 *   }
 * )
 */
class LikeDislikeFormatter extends LikeDislikeFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = 'en') {
    $elements = parent::viewElements($items, $langcode);
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
