# Runts Filter Drupal Module

> Runt: Single, short word at the end of a paragraph.

## Summary

This module provides a means of reducing or eliminating runts in your copy. It provides several ways of doing so, including a field formatter and text filter to be configured through the Drupal UI, as well as a PHP class to use at the code level by developers.

[Read more about runts, sometimes called orphans.](https://opusdesign.us/wordcount/typographic-widows-orphans/)

## Strategy

The strategy taken is to ensure that the final word-break(s) at the end of each paragraph is using the non-breaking space character. You can configure the number of replacements.

Original text:

```html
<p>Lorem ipsum dolar sit amet</p>
<p>My country 'tis of thee</p>
```

After processing:

```html
<p>Lorem ipsum dolar&amp;nbsp;sit&amp;nbsp;amet</p>
<p>My country 'tis&amp;nbsp;of&amp;nbsp;thee</p>
```

## Field Formatter

You may choose the _No Runts_ Format for certain entity fields when you go to the _Manage display_ page.

## Text Format

You will find a text filter called _No Runts Filter_ after enabling this module and entering the configuration page for any text format. See _/admin/config/content/formats_

## Developers

You may use this in code by doing something like the following:

```php
$text = 'Doorways to Our Childhood Selves';
$text = (new \Drupal\runts_filter\FilterRunts())
  ->setMinWordsPerLine(2)
  ->setNonCountingWords(['a', 'an'])($text);
$text === 'Doorways to Our Childhood&amp;nbsp;Selves'
```

## Testing

To run unit tests:

1. `composer install`
2. `./bin/run_unit_tests.sh`
