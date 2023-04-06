<?php

namespace Drupal\iq_blog_like_dislike\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'iq_blog_like_dislike' field type.
 *
 * @FieldType(
 *   id = "iq_blog_like_dislike",
 *   label = @Translation("Like Dislike"),
 *   description = @Translation("Like Dislike"),
 *   default_widget = "iq_blog_like_dislike_widget",
 *   default_formatter = "iq_blog_like_dislike_formatter"
 * )
 */
class LikeDislikeField extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'likes' => [
          'type' => 'varchar',
          'length' => 256,
          'not null' => FALSE,
        ],
        'dislikes' => [
          'type' => 'varchar',
          'length' => 256,
          'not null' => FALSE,
        ],
        'clicked_by' => [
          'type' => 'blob',
          'size' => 'big',
          'not null' => FALSE,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];
    $properties['likes'] = DataDefinition::create('string')
      ->setLabel(t('likes label'));
    $properties['dislikes'] = DataDefinition::create('string')
      ->setLabel(t('dislikes label'));
    $properties['clicked_by'] = DataDefinition::create('string')
      ->setLabel(t('clicked by label'));

    return $properties;
  }

}
