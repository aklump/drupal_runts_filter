<?php

namespace Drupal\runts_filter;


use Drupal\Core\Form\FormStateInterface;

trait FilterSettingsTrait {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['min_words_per_paragraph_prerequisite'] = array(
      '#type' => 'number',
      '#title' => $this->t('Minimum words required'),
      '#default_value' => $this->getMinWordsPerParagraphPrerequisite(),
      '#description' => $this->t('If a paragraph has less, the filter will not be applied to that paragraph.'),
    );

    $form['min_words_last_line'] = array(
      '#type' => 'number',
      '#title' => $this->t('Last line minimum'),
      '#default_value' => $this->getMinWordsLastLine(),
      '#description' => $this->t('The minimum words that should appear on the last line of a paragraph to eliminate the runt.'),
    );

    $form['ignored_words'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Ignored words'),
      '#default_value' => implode(' ', $this->getIgnoredWords()),
      '#description' => $this->t('Space-separated list of words that should not count, e.g. "a", "an", "the", etc.'),
    );

    return $form;
  }

  /**
   * Convert the stored value into an array of words.
   *
   * @param string $value
   *   A space- or comma-separated string of words.
   *
   * @return array
   *   The list of words as an array, separators removed.
   */
  protected function splitWordList(string $value): array {
    $value = preg_split('/[\s,]/', $value);

    return array_values(array_filter($value));
  }

}
