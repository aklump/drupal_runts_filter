<?php

namespace Drupal\runts_filter;

class FilterRunts {

  const SEPARATOR = 'SEPARATOR';

  const WORD = 'WORD';

  const NON_COUNTING_WORD = 'NON_COUNTING_WORD';

  const REGEX_WORD_SEP = '/^( |\t|&nbsp;)+/';

  const REGEX_PARAGRAPH_ENDING = '/^(<\/p>|\n\n)/';

  /**
   * @var array
   */
  private $config;

  public function setIgnoredWords(array $words): self {
    $this->config['ignored_words'] = array_map('strtolower', $words);

    return $this;
  }

  /**
   * Set min words needed to apply filter to paragraph.
   *
   * @param int $count
   *   The number of (counting) words that must appear in a single paragraph
   *   before the filter will run.
   *
   * @return $this
   */
  public function setMinWordsPerParagraphPrerequisite(int $count): self {
    $this->config['min_words_per_paragraph_prerequisite'] = $count;

    return $this;
  }

  public function setMinWordsLastLine(int $count): self {
    $this->config['min_words_last_line'] = $count;

    return $this;
  }

  public function __invoke(string $text): string {
    $paragraphs = $this->splitIntoParagraphs($text);
    foreach ($paragraphs as &$paragraph) {
      $paragraph = $this->processSingleParagraph($paragraph);
    }

    return implode('', $paragraphs);
  }

  private function splitIntoParagraphs(string $text): array {
    $paragraphs = [];
    $pos = 0;
    while ($pos < strlen($text)) {
      if (preg_match(self::REGEX_PARAGRAPH_ENDING, substr($text, $pos), $matches)) {
        $paragraph_start = $paragraph_start ?? 0;
        $paragraph_ending = $matches[0];
        $paragraph_length = $pos - $paragraph_start + strlen($paragraph_ending);
        $paragraphs[] = substr($text, $paragraph_start, $paragraph_length);

        $pos += strlen($paragraph_ending);
        $paragraph_start = $pos;
      }
      else {
        $pos++;
      }
    }

    $final_paragraph = substr($text, $paragraph_start ?? 0);
    if ($final_paragraph) {
      $paragraphs[] = $final_paragraph;
    }

    return $paragraphs;
  }

  private function processSingleParagraph(string $text): string {
    $tokens = $this->tokenize($text);
    $tokens = $this->removeTrailingSeparators($tokens);
    $word_count = count(array_filter($tokens, function (array $token) {
      return self::WORD === $token['type'];
    }));
    if ($word_count < ($this->config['min_words_per_paragraph_prerequisite'] ?? 2)) {
      return $text;
    }
    $remaining_replacement_count = ($this->config['min_words_last_line'] ?? 0);
    $index = count($tokens) - 1;

    // Starting at the end count backwards and replace separators with NBSP.
    while ($index > 0 && $remaining_replacement_count > 0) {
      if (self::SEPARATOR === $tokens[$index]['type']) {
        $tokens[$index]['value'] = '&nbsp;';
      }
      elseif (self::WORD === $tokens[$index]['type']) {
        --$remaining_replacement_count;
      }
      --$index;
    }

    return $this->serialize($tokens);
  }

  private function serialize(array $tokens): string {
    return implode('', array_map(function (array $token) {
      return $token['value'];
    }, $tokens));
  }

  /**
   * Split the text into tokens of words and splits.
   *
   * @param string $text
   *
   * @return array[]
   *   Each element is the:
   *   - 0 Token value.
   *   - 1 Token type.
   */
  private function tokenize(string $text): array {
    $tokens = [];
    $pos = 0;
    $word_start = NULL;
    while ($pos < strlen($text)) {
      if (preg_match(self::REGEX_WORD_SEP, substr($text, $pos), $matches)) {
        $separator = $matches[0];
        if (isset($word_start)) {
          $word_length = $pos - $word_start;
          $word = substr($text, $word_start, $word_length);
          $type = $this->isNonCountingWord($word) ? self::NON_COUNTING_WORD : self::WORD;
          $tokens[] = $this->token($type, $word, $word_start);
        }
        $tokens[] = $this->token(self::SEPARATOR, $separator, $pos);
        $word_start = $pos + strlen($separator);
        $pos += strlen($separator);
      }
      elseif (!isset($word_start)) {
        $word_start = 0;
      }
      $pos++;
    }

    $final_word = substr($text, $word_start);
    if ($final_word) {
      $type = $this->isNonCountingWord($final_word) ? self::NON_COUNTING_WORD : self::WORD;
      $tokens[] = $this->token($type, $final_word, $word_start);
    }

    return $tokens;
  }


  /**
   * Given an array of tokens, remove all sep tokens from the end backward.
   *
   * @param array $tokens
   *
   * @return array
   *   The original array without trailing separators.
   *
   * @see self::SEPARATOR
   */
  private function removeTrailingSeparators(array $tokens): array {
    $trailing = end($tokens);
    while ($trailing && self::SEPARATOR === $trailing['type'] && count($tokens)) {
      array_pop($tokens);
      $trailing = end($tokens);
    }

    return $tokens;
  }

  /**
   * Create a token array item.
   *
   * @param int $type
   * @param string $value
   * @param int $offset
   *
   * @return array
   */
  private function token(string $type, string $value, int $offset): array {
    return [
      'value' => $value,
      'type' => $type,
      'offset' => $offset,
      'length' => strlen($value),
    ];
  }

  private function isNonCountingWord(string $word): bool {
    if (empty($this->config['ignored_words'])) {
      return FALSE;
    }

    return in_array(strtolower($word), $this->config['ignored_words']);
  }

}
