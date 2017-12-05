<?php

namespace Drupal\config_reference_formatters\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Field formatter for entity reference fields that hold views.
 *
 * @FieldFormatter(
 *   id = "entity_reference_view",
 *   label = @Translation("Rendered view"),
 *   description = @Translation("Display the referenced view."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class ViewReferenceFormatter extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return parent::defaultSettings() + [
      'display' => 'default',
      'arguments' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    /** @var \Drupal\views\Entity\View[] $views */
    $views = [];
    /** @var \Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem $item */
    foreach ($items as $item) {
      /** @var \Drupal\Core\Entity\Plugin\DataType\EntityReference $reference */
      if (!($reference = $item->getProperties(TRUE)['entity'])) {
        continue;
      }

      $views[] = $reference->getTarget()->getValue();
    }

    $arguments = $this->getViewArgumentsComputed($items->getEntity());
    $viewBuilds = [];
    foreach ($views as $view) {
      // Don't include views that don't have the specified display.
      if (!($display = $view->getDisplay($this->settings['display']))) {
        continue;
      }

      $args = array_slice($arguments, 0, count($display['display_options']['arguments']));
      $exec = $view->getExecutable();

      $viewBuilds[] = $exec->buildRenderable($display['id'], $args);
    }

    return $viewBuilds;
  }

  /**
   * Gets the computed view arguments.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity to which this field belongs.
   *
   * @return array
   *   The computed arguments.
   */
  protected function getViewArgumentsComputed(FieldableEntityInterface $entity) {
    $arguments = $this->getViewArguments();

    foreach ($arguments as $i => $argument) {
      $arguments[$i] = preg_replace_callback('/\{\{([a-z0-9_ ]+)\}\}/', function ($matches) use ($entity) {
        if ($field = $entity->get($matches[1])) {
          return $field->getString();
        }

        return $matches[0];
      }, $argument);
    }

    return $arguments;
  }

  /**
   * Gets the list of set arguments.
   *
   * @return array
   *   Argument settings.
   */
  protected function getViewArguments() {
    $arguments = $this->settings['arguments'];
    foreach ($arguments as $i => $argument) {
      if (!is_int($i) || !$argument) {
        unset($arguments[$i]);
      }
    }

    time();
    return $arguments;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $summary[] = 'Display: ' . $this->settings['display'];

    if ($arguments = $this->getViewArguments()) {
      $summary[] = 'Args: ' . implode('/', $arguments);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form += parent::settingsForm($form, $form_state);

    $form['display'] = [
      '#type' => 'textfield',
      '#title' => $this->t('View display ID'),
      '#default_value' => $this->settings['display'],
      '#required' => TRUE,
    ];

    $argumentsId = Html::getUniqueId('arguments_list');
    $form['arguments'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('Use {{field_name}} pattern for access to the entities fields. These will be replaced by the entities field value, if it exists.'),
      '#title' => $this->t('Contextual arguments'),
      '#prefix' => "<div id='$argumentsId'>",
      '#suffix' => '</div>',
      '#tree' => TRUE,
    ];

    foreach ($this->getViewArguments() as $argument) {
      $form['arguments'][] = [
        '#type' => 'textfield',
        '#value' => $argument,
      ];
    }

    for ($i = 0; $i < $form_state->get('argument_count') ?: 0; ++$i) {
      $form['arguments'][] = [
        '#type' => 'textfield',
        '#value' => '',
      ];
    }

    $form['arguments']['add-more'] = [
      '#type' => 'submit',
      '#op' => 'add-argument',
      '#value' => $this->t('Add argument'),
      '#submit' => [[$this, 'addArgumentSubmit']],
      '#limit_validation_errors' => [],
      '#ajax' => [
        'callback' => [$this, 'addArgumentAjax'],
        'wrapper' => $argumentsId,
      ],
    ];

    return $form;
  }

  /**
   * Adds new field for view contextual argument.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The argument fields render array.
   */
  public function addArgumentAjax(array $form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement();

    // Go one level up in the form, to the widgets container.
    $element = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -1));
    return $element;
  }

  /**
   * Increments the argument field count and rebuilds the form.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function addArgumentSubmit(array $form, FormStateInterface $form_state) {
    $count = $form_state->get('argument_count') ?: 0;
    $form_state->set('argument_count', ++$count);
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return $field_definition->getSetting('handler') == 'default:view';
  }

}
