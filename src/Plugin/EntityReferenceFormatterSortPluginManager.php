<?php

namespace Drupal\config_reference_formatters\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Class EntityReferenceFormatterSortPluginManager.
 *
 * @package Drupal\config_reference_formatters\Plugin
 */
class EntityReferenceFormatterSortPluginManager extends DefaultPluginManager {

  /**
   * EntityReferenceFormatterSortPluginManager constructor.
   *
   * @param \Traversable $namespaces
   *   The namespaces.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/EntityReferenceFormatterSort',
      $namespaces,
      $module_handler,
      'Drupal\config_reference_formatters\Plugin\EntityReferenceFormatterSortPluginInterface',
      'Drupal\config_reference_formatters\Annotation\EntityReferenceFormatterSort');

    $this->alterInfo('entity_reference_formatter_sort_info');
    $this->setCacheBackend($cache_backend, 'entity_reference_formatter_sort_types_plugins');
  }

}
