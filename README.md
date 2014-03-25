# Analytics

Port of Laravel 3 bundle [lordcoste/analytics-s2s](https://github.com/lordcoste/analytics-s2s) for Laravel 4

[![Build Status](https://travis-ci.org/thujohn/analytics-l4.png?branch=master)](https://travis-ci.org/thujohn/analytics-l4)


## Installation

Add `thujohn/analytics` to `composer.json`.

    "thujohn/analytics": "dev-master"
    
Run `composer update` to pull down the latest version of Analytics.

Now open up `app/config/app.php` and add the service provider to your `providers` array.

    'providers' => array(
        'Thujohn\Analytics\AnalyticsServiceProvider',
    )

Now add the alias.

    'aliases' => array(
        'Analytics' => 'Thujohn\Analytics\AnalyticsFacade',
    )


## Configuration

Run `php artisan config:publish thujohn/analytics` and modify the config file with your own informations.

## Usage
Querying the API for visits and pageviews in the last week.

More information about this calling the Google Analytics API can be found here https://developers.google.com/apis-explorer/#s/analytics/v3/analytics.data.ga.get
```php
$site_id = Analytics::getSiteIdByUrl('http://github.com/'); // return something like 'ga:11111111'

$stats = Analytics::query($siteID, '7daysAgo', 'yesterday', 'ga:visits,ga:pageviews');
```
