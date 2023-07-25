<?php

namespace Drupal\runts_filter\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\runts_filter\FilterRunts;

/**
 * @Filter(
 *   id = "filter_runts",
 *   title = @Translation("No Runts Filter"),
 *   description = @Translation("Prevent single words at the end of a paragraph."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 * )
 */
class RuntsFilter extends FilterBase {

  /**
   * Prepares the text for processing.
   *
   * Filters should not use the prepare method for anything other than escaping,
   * because that would short-circuit the control the user has over the order in
   * which filters are applied.
   *
   * @param string $text
   *   The text string to be filtered.
   * @param string $langcode
   *   The language code of the text to be filtered.
   *
   * @return string
   *   The prepared, escaped text.
   */
  public function prepare($text, $langcode) {

  }

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
    $processor = new FilterRunts();
    $processor
      ->setMinWordsPerLine(3)
      ->setNonCountingWords(['an', 'a', 'of']);
    $text = $processor($text);

    return new FilterProcessResult($text);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['foo'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Foo'),
      '#default_value' => $this->settings['foo'] ?? NULL,
      '#description' => $this->t('Lorem ipsum dolar.'),
    );

    return $form;
  }

}
