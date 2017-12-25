Kochava's web api documentations
================================

[Official doc](https://support.kochava.com/analytics-reports-api/api-v1-3-requesting-and-scheduling-reports)

Description
===========

This SDK not fully implemented according to the official doc
Feel free to add a new code to the SDK, but do not forget about unit tests

By default a report use:
 + ```delivery_format``` as JSON", "CSV" not implemented
 + a timezone is UTC

Obtaining reports are based on queues, this means several request to the api:

 1. First call - add request to the queue of report
 2. Next calls are pinging to obtain a report status, default frequency is 10 sec 
 3. Last call - get the report

if pass a long period, preparing the perort can takes a long time of script execution
 
Installation
============

```shell
composer require f-dg/kochava-api-sdk:dev-master
```
or via composer.json

```json
  "require": {
    "asignal/kochava-api-sdk": "dev-master"
  },
```

or via git clone

```shell
git clone git@github.com:f-dg/kochava-api-sdk.git
```

Example of fetching list of applications
========================================

```php
use \KochavaReporting\ReportSummary,
    \KochavaReporting\ReportSummaryValues,
    \KochavaReporting\CurlAdapter;

/**
 *  you can use either "ReportSummary" or "ReportDetail" 
 *  to get list of applications, it doesn't metter
 */
$kochavaSDK = new ReportSummary(new CurlAdapter(), $token);

/**
 *   array(2) {
 *       [0] => array(6) {
 *           'status' => "OK",
 *           'guid' => "app-ios-test-xpv",
 *           'app_name' => "App - iOS Dev",
 *           'app_id' => "19897",
 *           'account_name' => "Country Financial",
 *           'platform' => "ios",
 *       }
 *       [1] => array(6) {
 *           'status' => "OK",
 *           'guid' => "app-android-dev-2zrzfk3",
 *           'app_name' => "App - Android Dev",
 *           'app_id' => "20254",
 *           'account_name' => "Country Financial",
 *           'platform' => "android",
 *       }
 *   }
 */ 
$apps = $kochavaSDK->getApplications();
```

Example of fetching a Summary Report
====================================

```php
use \KochavaReporting\ReportSummary,
    \KochavaReporting\ReportSummaryValues,
    \KochavaReporting\CurlAdapter;

    $reportSDK = new ReportSummary(
        new CurlAdapter(),
        $kochavaToken,
        $kochavaAppGUID // received from "getApplication" - "app-android-dev-2zrzfk3"
    );

    $period = [
        'from' => '1497819600',
        'to' => '1497906000'
    ];

    /**
     *  Traffic type and counters needed on which to report.
     */
    $traffic = ReportSummaryValues::getTrafficList();

    try { 

        /**
         * structure of call
         * check a column "Valid in Report" is supported for "Summary"
         * https://support.kochava.com/analytics-reports-api/api-v1-3-call-structure
         */
        $requestedParams = [
            ReportSummaryValues::FIELD_TIME_START         => $period['from'],
            ReportSummaryValues::FIELD_TIME_END           => $period['to'],
            ReportSummaryValues::FIELD_CUSTOM_REPORT_NAME => reset($traffic),
            ReportSummaryValues::FIELD_TRAFFIC            => $traffic,
            ReportSummaryValues::FIELD_TRAFFIC_FILTERING  => [
                ReportSummaryValues::FIELD_TRAFFIC_FILTERING_NETWORK => [
                    ReportSummaryValues::FACEBOOK_NETRWORK_ID,
                ],
            ],
            /**
             *  FIELD_TIME_SIRIES is grouping period  
             *  1=1 hour, 4=4 hour, blocks (12-16, 16-20, 20-24)
             *  if 24, group by day
             */
            ReportSummaryValues::FIELD_TIME_SIRIES        => '1',
            ReportSummaryValues::FIELD_TRAFFIC_GROUPING   => [
                ReportSummaryValues::TRAFFIC_GROUPING_NETWORK,
                ReportSummaryValues::TRAFFIC_GROUPING_CAMPAIGN,
                ReportSummaryValues::TRAFFIC_GROUPING_TRACKER,
            ],
        ];

        $report = $reportSDK->getReport($requestedParams);

    } catch (\Exception $e) {
        echo 'Api response: '.$e->getMessage().PHP_EOL;
    }
```


Example of fetching a Detail Report
====================================

```php
use \KochavaReporting\ReportDetail,
    \KochavaReporting\ReportDetailValues,
    \KochavaReporting\CurlAdapter;

    $reportSDK = new ReportDetail(
        new CurlAdapter(),
        $kochavaToken,
        $kochavaAppGUID // received from "getApplication" - "app-android-dev-2zrzfk3"
    );

    $period = [
        'from' => '1497819600',
        'to' => '1497906000'
    ];

    /**
     *  Traffic type and counters needed on which to report.
     */
    $traffic = ReportDetailValues::getTrafficList();

    $errors = []; 
    $statstics = [];

    /**
     *  per request, for Detail Report you can pass to the FIELD_TRAFFIC 
     *  only an array with one traffic
     *  that's why used "foreach" to iterate $traffic
     */
    foreach ($traffic as $trafficValue) {

        try { 

            /**
             * structure of call
             * check a column "Valid in Report" is supported for "Detail"
             * https://support.kochava.com/analytics-reports-api/api-v1-3-call-structure
             */
            $requestedParams = [
                ReportDetailValues::FIELD_TIME_START         => $period['from'],
                ReportDetailValues::FIELD_TIME_END           => $period['to'],
                ReportDetailValues::FIELD_CUSTOM_REPORT_NAME => $trafficValue,
                ReportDetailValues::FIELD_TRAFFIC            => [
                    $trafficValue
                ],
                ReportDetailValues::FIELD_TRAFFIC_FILTERING  => [
                    ReportDetailValues::FIELD_TRAFFIC_FILTERING_NETWORK => [
                        ReportDetailValues::FACEBOOK_NETRWORK_ID,
                    ],
                ],
            ];

            $statistics[$trafficValue] = $reportSDK->getReport($requestedParams);

        } catch (\Exception $e) {
            $errors[$trafficValue] = $e->getMessage().PHP_EOL;
        }

    }
```

Unit tests
----------

```shell
cd path/to/kochava-api-sdk/test && phpunit -v -c ../phpunit.xml . --coverage-text
```
