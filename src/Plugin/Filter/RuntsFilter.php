<?php

namespace Drupal\runts_filter\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\runts_filter\FilterRunts;
use Drupal\runts_filter\FilterSettingsInterface;
use Drupal\runts_filter\FilterSettingsTrait;

/**
 * @Filter(
 *   id = "filter_runts",
 *   title = @Translation("No Runts Filter"),
 *   description = @Translation("Prevent single words at the end of a paragraph."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 * )
 */
class RuntsFilter extends FilterBase implements FilterSettingsInterface {

  use FilterSettingsTrait;

  /**
   * Performs the filter processing.
   *
   * @param string $text
   *   The text string to be filtered.
   * @param string $langcode
   *   The language code of the text to be filtered.
   *
   * @return \Drupal\filter\FilterProcessResult
   *   The filtered text, wrapped in a FilterProcessResult object, and possibly
   *   with associated assets, cacheability metadata and placeholders.
   *
   * @see \Drupal\filter\FilterProcessResult
   */
  public function process($text, $langcode) {
    if ($text) {
      $processor = new FilterRunts();
      $processor
        ->setMinWordsPerLine($this->getMinWordsPerLine())
        ->setNonCountingWords($this->getNonCountingWords());
      $text = $processor($text);
    }

    return new FilterProcessResult($text);
  }

  public function getNonCountingWords(): array {
    $value = $this->settings['non_counting_words'] ?? FilterSettingsInterface::NON_COUNTING_WORDS;

    return $this->splitNonCountingWords($value);
  }

  public function getMinWordsPerLine(): int {
    return (int) ($this->settings['min_words_per_line'] ?? FilterSettingsInterface::MIN_WORDS_PER_LINE);
  }

}
