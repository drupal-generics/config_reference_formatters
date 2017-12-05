<?php

namespace Drupal\config_reference_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\config_reference_formatters\Plugin\EntityReferenceFormatterSortPluginManager;

/**
 * Plugin implementation of the 'entity reference rendered entity' formatter.
 *
 * Improvements:
 * - Sort the referenced elements.
 * - Limit the number of referenced elements to display.
 *
 * @FieldFormatter(
 *   id = "config_reference_formatters_entity_reference_entity_view",
 *   label = @Translation("Rendered entity (improved)"),
 *   description = @Translation("Display the referenced entities rendered by entity_view()."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class ImprovedEntityReferenceEntityFormatter extends EntityReferenceEntityFormatter {

  use EntityReferenceFormatterSortTrait,
    EntityReferenceFormatterLimitTrait
  {
    EntityReferenceFormatterSortTrait::defaultSettings    as sortDefaultSettings;
    EntityReferenceFormatterSortTrait::settingsForm       as sortSettingsForm;
    EntityReferenceFormatterSortTrait::settingsSummary    as sortSettingsSummary;
    EntityReferenceFormatterSortTrait::getEntitiesToView  as sortGetEntitiesToView;
    EntityReferenceFormatterLimitTrait::defaultSettings   as limitDefaultSettings;
    EntityReferenceFormatterLimitTrait::settingsForm      as limitSettingsForm;
    EntityReferenceFormatterLimitTrait::settingsSummary   as limitSettingsSummary;
    EntityReferenceFormatterLimitTrait::getEntitiesToView as limitGetEntitiesToView;
  }

  /**
   * ImprovedEntityReferenceEntityFormatter constructor.
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
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository
   *   The entity display repository.
   * @param \Drupal\config_reference_formatters\Plugin\EntityReferenceFormatterSortPluginManager $sort_plugin_manager
   *   The entity reference formatter sort plugin manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, LoggerChannelFactoryInterface $logger_factory, EntityTypeManagerInterface $entity_type_manager, EntityDisplayRepositoryInterface $entity_display_repository, EntityReferenceFormatterSortPluginManager $sort_plugin_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $logger_factory, $entity_type_manager, $entity_display_repository);
    $this->sortPluginManager = $sort_plugin_manager;
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
      $container->get('logger.factory'),
      $container->get('entity_type.manager'),
      $container->get('entity_display.repository'),
      $container->get('config_reference_formatters.plugin.manager.entity_reference_formatter_sort')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return self::sortDefaultSettings()
      + self::limitDefaultSettings()
      + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements += $this->sortSettingsForm($form, $form_state);
    $elements += $this->limitSettingsForm($form, $form_state);

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $summary = array_merge($summary,
      $this->sortSettingsSummary(),
      $this->limitSettingsSummary());

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntitiesToView(EntityReferenceFieldItemListInterface $items, $langcode) {
    $entities = parent::getEntitiesToView($items, $langcode);

    $entities = $this->sortGetEntitiesToView($entities);
    $entities = $this->limitGetEntitiesToView($entities);

    return $entities;
  }

}
