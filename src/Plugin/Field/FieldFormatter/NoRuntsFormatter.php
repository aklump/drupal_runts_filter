<?php

namespace Drupal\runts_filter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\runts_filter\FilterSettingsInterface;
use Drupal\runts_filter\FilterSettingsTrait;

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
class NoRuntsFormatter extends FormatterBase implements FilterSettingsInterface {

  use FilterSettingsTrait;

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $filter_config = [
      'min_words_per_line' => $this->getSetting('min_words_per_line'),
      'non_counting_words' => $this->getNonCountingWords(),
    ];

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
      'min_words_per_line' => FilterSettingsInterface::MIN_WORDS_PER_LINE,
      'non_counting_words' => FilterSettingsInterface::NON_COUNTING_WORDS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('<strong>Min words:</strong> @value', [
      '@value' => $this->getMinWordsPerLine(),
    ]);
    $summary[] = $this->t('<strong>Non words:</strong> @value', [
      '@value' => implode(' ', $this->getNonCountingWords()),
    ]);

    return $summary;
  }

  public function getNonCountingWords(): array {
    $value = $this->getSetting('non_counting_words') ?? FilterSettingsInterface::NON_COUNTING_WORDS;

    return $this->splitNonCountingWords($value);
  }

  public function getMinWordsPerLine(): int {
    return (int) ($this->getSetting('min_words_per_line') ?? FilterSettingsInterface::MIN_WORDS_PER_LINE);
  }

}
