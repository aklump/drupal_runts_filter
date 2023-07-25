<?php


namespace Drupal\Tests\runts_filter;

use PHPUnit\Framework\TestCase;
use Drupal\runts_filter\FilterRunts;

/**
 * @group extensions
 * @group runts_filter
 * @covers \Drupal\runts_filter\FilterRunts
 */
final class FilterRuntsTest extends TestCase {

  /**
   * Provides data for testFilterRuntsWorksAsExpected.
   */
  public function dataForTestFilterRuntsWorksAsExpectedProvider() {
    $tests = [];
    $tests[] = [
      [
        'setMinWordsPerLine' => 3,
        'setNonCountingWords' => ['an', 'of'],
      ],
      'A series of essays that weave an ethic of education with an exploration of story',
      'A series of essays that weave an ethic of education with&nbsp;an&nbsp;exploration&nbsp;of&nbsp;story',
    ];

    $tests[] = [
      [
        'setMinWordsPerLine' => 3,
        'setNonCountingWords' => ['amet', 'sit'],
      ],
      'Lorem ipsum dolar sit amet',
      'Lorem&nbsp;ipsum&nbsp;dolar&nbsp;sit&nbsp;amet',
    ];
    $tests[] = [
      [
        'setMinWordsPerLine' => 3,
        'setNonCountingWords' => [],
      ],
      'Lorem ipsum dolar sit amet',
      'Lorem ipsum dolar&nbsp;sit&nbsp;amet',
    ];
    $tests[] = [
      ['setMinWordsPerLine' => 3],
      'A series exploration&nbsp;of&nbsp;story',
      'A series exploration&nbsp;of&nbsp;story',
    ];
    $tests[] = [
      ['setMinWordsPerLine' => 2],
      'Lorem ipsum    ',
      'Lorem&nbsp;ipsum',
    ];
    $tests[] = [
      [],
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    ];
    $tests[] = [
      ['setMinWordsPerLine' => 3],
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore&nbsp;magna&nbsp;aliqua.',
    ];
    $tests[] = [
      ['setMinWordsPerLine' => 5],
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore&nbsp;et&nbsp;dolore&nbsp;magna&nbsp;aliqua.',
    ];

    return $tests;
  }

  /**
   * @dataProvider dataForTestFilterRuntsWorksAsExpectedProvider
   */
  public function testFilterRuntsWorksAsExpected($config, $text, $expected) {
    $filter = new FilterRunts();
    foreach ($config as $method => $value) {
      $filter->$method($value);
    }
    $this->assertSame($expected, $filter($text));
  }

  public function _testSetMinWordsPerLineReturnsSelf() {
    $filter = new FilterRunts();
    $this->assertSame($filter, $filter->setMinWordsPerLine(5));
  }

  public function _testSetNonCountingWordsReturnsSelf() {
    $filter = new FilterRunts();
    $this->assertSame($filter, $filter->setNonCountingWords([
      'of',
      'the',
      'a',
      'an',
    ]));
  }

}
