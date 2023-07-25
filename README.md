# Runts Filter Drupal Module

## Summary

## Installation

1. Download this module to _web/modules/custom/runts_filter_.
1. Add the following to the application's _composer.json_ above web root.

    ```json
    {
      "repositories": [
        {
          "type": "path",
          "url": "app/install/modules/runts_filter"
        }
      ]
    }
    ```

1. Now run `composer require drupal/runts-filter:@dev`
1. Enable this module.

## Configuration

        $config['runts_filter.settings']['foo'] = 'bar;
