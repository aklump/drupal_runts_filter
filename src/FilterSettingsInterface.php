<?php

namespace Drupal\runts_filter;

interface FilterSettingsInterface {

  const IGNORED_WORDS = 'a an and at by for of the to';

  const MIN_WORDS_PER_PARAGRAPH_PREREQUISITE = 8;

  const MIN_WORDS_LAST_LINE = 2;

  public function getIgnoredWords(): array;

  public function getMinWordsPerParagraphPrerequisite(): int;

  public function getMinWordsLastLine(): int;

}
