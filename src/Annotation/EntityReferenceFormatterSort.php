<?php

namespace Drupal\config_reference_formatters\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Class EntityReferenceFormatterSort.
 *
 * Annotation for plugin that performs sorting of entity reference formatter
 * elements.
 *
 * @package Drupal\config_reference_formatters\Annotation
 *
 * @Annotation
 */
class EntityReferenceFormatterSort extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the formatter type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
