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
        ->setMinWordsPerParagraphPrerequisite($this->getMinWordsPerParagraphPrerequisite())
        ->setMinWordsLastLine($this->getMinWordsLastLine())
        ->setIgnoredWords($this->getIgnoredWords());
      $text = $processor($text);
    }

    return new FilterProcessResult($text);
  }

  public function getIgnoredWords(): array {
    $value = $this->settings['ignored_words'] ?? FilterSettingsInterface::IGNORED_WORDS;

    return $this->splitWordList($value);
  }

  public function getMinWordsLastLine(): int {
    return (int) ($this->settings['min_words_last_line'] ?? FilterSettingsInterface::MIN_WORDS_LAST_LINE);
  }

  public function getMinWordsPerParagraphPrerequisite(): int {
    return (int) ($this->settings['min_words_per_paragraph_prerequisite'] ?? FilterSettingsInterface::MIN_WORDS_PER_PARAGRAPH_PREREQUISITE);
  }

}
