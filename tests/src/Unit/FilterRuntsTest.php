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
        'setMinWordsLastLine' => 2,
        'setMinWordsPerParagraphPrerequisite' => 4,
      ],
      "Lorem ipsum dolar",
      "Lorem ipsum dolar",
    ];
    $tests[] = [
      [
        'setMinWordsLastLine' => 2,
        'setMinWordsPerParagraphPrerequisite' => 4,
      ],
      "Lorem ipsum dolar sit",
      "Lorem ipsum dolar&nbsp;sit",
    ];

    $tests[] = [
      [
        'setMinWordsLastLine' => 3,
      ],
      "Lorem ipsum dolar sit amet\n\nMy country \'tis of thee",
      "Lorem ipsum dolar&nbsp;sit&nbsp;amet\n\nMy country \'tis&nbsp;of&nbsp;thee",
    ];

    $tests[] = [
      [
        'setMinWordsLastLine' => 3,
      ],
      '<p>Lorem ipsum dolar sit amet</p><p>My country \'tis of thee</p>',
      '<p>Lorem ipsum dolar&nbsp;sit&nbsp;amet</p><p>My country \'tis&nbsp;of&nbsp;thee</p>',
    ];
    $tests[] = [
      [
        'setMinWordsLastLine' => 3,
        'setIgnoredWords' => ['an', 'of'],
      ],
      'A series of essays that weave an ethic of education with an exploration of story',
      'A series of essays that weave an ethic of education with&nbsp;an&nbsp;exploration&nbsp;of&nbsp;story',
    ];
    $tests[] = [
      [
        'setMinWordsLastLine' => 3,
        'setIgnoredWords' => ['amet', 'sit'],
      ],
      'Lorem ipsum dolar sit amet',
      'Lorem&nbsp;ipsum&nbsp;dolar&nbsp;sit&nbsp;amet',
    ];
    $tests[] = [
      [
        'setMinWordsLastLine' => 3,
        'setIgnoredWords' => [],
      ],
      'Lorem ipsum dolar sit amet',
      'Lorem ipsum dolar&nbsp;sit&nbsp;amet',
    ];
    $tests[] = [
      ['setMinWordsLastLine' => 3],
      'A series exploration&nbsp;of&nbsp;story',
      'A series exploration&nbsp;of&nbsp;story',
    ];
    $tests[] = [
      ['setMinWordsLastLine' => 2],
      'Lorem ipsum    ',
      'Lorem&nbsp;ipsum',
    ];
    $tests[] = [
      [],
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    ];
    $tests[] = [
      ['setMinWordsLastLine' => 3],
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore&nbsp;magna&nbsp;aliqua.',
    ];
    $tests[] = [
      ['setMinWordsLastLine' => 5],
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

  public function testsetMinWordsPerParagraphPrerequisiteReturnsSelf() {
    $filter = new FilterRunts();
    $this->assertSame($filter, $filter->setMinWordsPerParagraphPrerequisite(3));
  }

  public function testSetMinWordsLastLineReturnsSelf() {
    $filter = new FilterRunts();
    $this->assertSame($filter, $filter->setMinWordsLastLine(5));
  }

  public function testSetIgnoredWordsReturnsSelf() {
    $filter = new FilterRunts();
    $this->assertSame($filter, $filter->setIgnoredWords([
      'of',
      'the',
      'a',
      'an',
    ]));
  }

}
