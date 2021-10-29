<?php

namespace Drupal\iq_blog\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\term_reference_tree\Plugin\Field\FieldFormatter\TermReferenceTree;

/**
 * Field formatter to show terms with depth 0.
 *
 * @FieldFormatter(
 *   id = "iq_term_reference_tree",
 *   label = @Translation("iqual Term Reference Tree"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class TermReferenceTreeDepth extends TermReferenceTree {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $data = [];
    foreach ($items->getValue() as $item) {
      $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($item['target_id']);
      if (!$term->parent->target_id) {
        $data[] = $item;
      }
    }

    $element[] = [
      '#theme' => 'term_tree_list',
      '#data' => $data,
      '#attached' => ['library' => ['term_reference_tree/term_reference_tree_css']],
    ];
    return $element;
  }

}
