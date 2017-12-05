<?php

namespace Drupal\config_reference_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Plugin\Exception\PluginException;

/**
 * Trait EntityReferenceFormatterSortTrait.
 *
 * Allows to sort the referenced elements.
 *
 * @package Drupal\config_reference_formatters\Plugin\Field\FieldFormatter
 *
 * @see \Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase
 *   Use this trait only for classes which extend EntityReferenceFormatterBase.
 */
trait EntityReferenceFormatterSortTrait {

  /**
   * The entity reference formatter sort plugin manager.
   *
   * @var \Drupal\config_reference_formatters\Plugin\EntityReferenceFormatterSortPluginManager
   */
  protected $sortPluginManager;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'elements_order' => 'default',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $options = [
      'default' => $this->t('Default'),
    ];

    foreach ($this->sortPluginManager->getDefinitions() as $plugin_id => $plugin_definition) {
      $options[$plugin_id] = $plugin_definition['label'];
    }

    $elements['elements_order'] = [
      '#type' => 'select',
      '#title' => $this->t('Elements order'),
      '#description' => $this->t('Sort the referenced elements.'),
      '#options' => $options,
      '#default_value' => $this->getSetting('elements_order'),
      '#required' => TRUE,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    if ($plugin_definition = $this->sortPluginManager->getDefinition($this->getSetting('elements_order'), FALSE)) {
      $order = $plugin_definition['label'];
    }
    else {
      $order = $this->t('Default');
    }
    $summary[] = t('Elements order by: @order', ['@order' => $order]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntitiesToView($entities) {
    try {
      $plugin_id = $this->getSetting('elements_order');
      if ($plugin_id != 'default') {
        /** @var \Drupal\config_reference_formatters\Plugin\EntityReferenceFormatterSortPluginInterface $plugin */
        $plugin = $this->sortPluginManager->createInstance($plugin_id);
        $plugin->sortEntities($entities);
      }
    }
    catch (PluginException $e) {
    }

    return $entities;
  }

}
