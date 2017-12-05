<?php

namespace Drupal\config_reference_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;

/**
 * Trait EntityReferenceFormatterLimitTrait.
 *
 * Allows to limit the number of referenced elements to display.
 *
 * @package Drupal\config_reference_formatters\Plugin\Field\FieldFormatter
 *
 * @see \Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase
 *   Use this trait only for classes which extend EntityReferenceFormatterBase.
 */
trait EntityReferenceFormatterLimitTrait {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'elements_limit' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['elements_limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Elements limit'),
      '#description' => $this->t('Limit the number of referenced elements to display.'),
      '#default_value' => $this->getSetting('elements_limit'),
      '#min' => 1,
      '#step' => 1,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $limit = $this->getSetting('elements_limit') ? $this->getSetting('elements_limit') : $this->t('Unlimited');
    $summary[] = t('Elements limit: @limit', ['@limit' => $limit]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntitiesToView($entities) {
    if ($limit = $this->getSetting('elements_limit')) {
      return array_slice($entities, 0, $limit, TRUE);
    }

    return $entities;
  }

}
