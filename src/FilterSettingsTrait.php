<?php

namespace Drupal\runts_filter;


use Drupal\Core\Form\FormStateInterface;

trait FilterSettingsTrait {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['min_words_per_line'] = array(
      '#type' => 'number',
      '#title' => $this->t('Per line minimum'),
      '#default_value' => $this->getMinWordsPerLine(),
      '#description' => $this->t('The minimum words to keep on the last line of a paragraph.'),
    );

    $form['non_counting_words'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Non-counting words'),
      '#default_value' => implode(' ', $this->getNonCountingWords()),
      '#description' => $this->t('Space-separated words that should not count toward the line mininum'),
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
  protected function splitNonCountingWords(string $value): array {
    $value = preg_split('/[\s,]/', $value);

    return array_values(array_filter($value));
  }

}
