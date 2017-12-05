<?php

namespace Drupal\config_reference_formatters\Plugin;

/**
 * Interface EntityReferenceFormatterSortPluginInterface.
 *
 * @package Drupal\config_reference_formatters\Plugin
 */
interface EntityReferenceFormatterSortPluginInterface {

  /**
   * Gets the label of the plugin instance.
   *
   * @return string
   *   The label of the plugin instance.
   */
  public function getLabel();

  /**
   * Sorts referenced entities.
   *
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   The array of referenced entities to sort, keyed by delta.
   */
  public function sortEntities(array &$entities);

}
