<?php

namespace Drupal\config_reference_formatters\Plugin;

use Drupal\Core\Plugin\PluginBase;

/**
 * Class EntityReferenceFormatterSortPluginBase.
 *
 * @package Drupal\config_reference_formatters\Plugin
 */
abstract class EntityReferenceFormatterSortPluginBase extends PluginBase implements EntityReferenceFormatterSortPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    $this->pluginDefinition['label'];
  }

}
