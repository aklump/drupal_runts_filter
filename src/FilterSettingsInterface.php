<?php

namespace Drupal\runts_filter;

interface FilterSettingsInterface {

  const NON_COUNTING_WORDS = 'a an the of in on';

  const MIN_WORDS_PER_LINE = 3;

  public function getNonCountingWords(): array;

  public function getMinWordsPerLine(): int;
}
