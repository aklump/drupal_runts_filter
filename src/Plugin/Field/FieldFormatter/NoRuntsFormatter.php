<?php

namespace Drupal\runts_filter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the runts_filter_no_runts formatter.
 *
 * @FieldFormatter(
 *   id = "runts_filter_no_runts",
 *   module = "runts_filter",
 *   label = @Translation("No Runts"),
 *   field_types = {
 *     "string",
 *     "string_long",
 *     "text",
 *     "text_long",
 *     "text_with_summary",
 *   }
 * )
 */
class NoRuntsFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // TODO Populate from formatter settings.
    $filter_config = [];

    $elements = [];
    foreach ($items as $delta => $item) {
      $value = $item->get('value');
      $text = $value->getValue();
      $text = \Drupal::service('plugin.manager.filter')
        ->createInstance('filter_runts', $filter_config)
        ->process($text, $item->getLangcode());
      $value->setValue($text);
      $elements[$delta] = ['#markup' => $item->value];
    }

    return $elements;
  }

  /**
   * Defines the default settings for this plugin.
   *
   * @return array
   *   A list of default settings, keyed by the setting name.
   */
  public static function defaultSettings() {
    return [
      'foo' => 'bar',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $default = $this->getSetting('foo');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('<strong>Foo:</strong> @value', [
      '@value' => $this->getSetting('foo'),
    ]);

    return $summary;
  }

}
