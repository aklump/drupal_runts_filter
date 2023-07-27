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
      'min_words_per_paragraph_prerequisite' => $this->getMinWordsPerParagraphPrerequisite(),
      'min_words_last_line' => $this->getMinWordsLastLine(),
      'ignored_words' => $this->getIgnoredWords(),
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
      'min_words_per_paragraph_prerequisite' => FilterSettingsInterface::MIN_WORDS_PER_PARAGRAPH_PREREQUISITE,
      'min_words_last_line' => FilterSettingsInterface::MIN_WORDS_LAST_LINE,
      'ignored_words' => FilterSettingsInterface::IGNORED_WORDS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('<strong>Min words prereq:</strong> @value', [
      '@value' => $this->getMinWordsPerParagraphPrerequisite(),
    ]);
    $summary[] = $this->t('<strong>Min last line:</strong> @value', [
      '@value' => $this->getMinWordsLastLine(),
    ]);
    $summary[] = $this->t('<strong>Ignored:</strong> @value', [
      '@value' => implode(' ', $this->getIgnoredWords()),
    ]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function getMinWordsPerParagraphPrerequisite(): int {
    return (int) $this->getSetting('min_words_per_paragraph_prerequisite');
  }

  /**
   * {@inheritdoc}
   */
  public function getIgnoredWords(): array {
    $value = $this->getSetting('ignored_words');

    return $this->splitWordList($value);
  }

  /**
   * {@inheritdoc}
   */
  public function getMinWordsLastLine(): int {
    return (int) $this->getSetting('min_words_last_line');
  }

}
