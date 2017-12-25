<?php

namespace KochavaReporting;

class ReportSummaryException extends \Exception {}

use KochavaReporting\AbstractReport,
    KochavaReporting\ReportSummaryValues;

class ReportSummary extends AbstractReport
{
    const ENDPOINT = 'summary';

    /**
     * @throw ReportSummaryException
     */
    protected function addReportToQueue(array $requestParams)
    {
        $endpoint = $this->getEndpoint(static::ENDPOINT);
        $requestParams = array_merge(
            $this->getDefaultParams(),
            $requestParams
        );

        if (empty($requestParams[ReportSummaryValues::FIELD_TIME_SIRIES])) {
            throw new ReportSummaryException('Field "' . ReportSummaryValues::FIELD_TIME_SIRIES . '" is required');
        }

        $this->_deliveryFormat = $requestParams[ReportSummaryValues::FIELD_DELIVERY_FORMAT];

        $job = $this->_adapter->postRequest($endpoint, $requestParams);
        return $job;
    }

    protected function getDefaultParams()
    {
        return [
            AbstractReportValues::FIELD_API_KEY        => $this->_apiKey,
            AbstractReportValues::FIELD_APP_GUID       => $this->_appGuid,
            ReportSummaryValues::FIELD_DELIVERY_FORMAT => static::DELIVERY_FORMAT_JSON,
            ReportSummaryValues::FIELD_TIME_ZONE       => static::DEFAULT_TIME_ZONE,
            ReportSummaryValues::FIELD_TRAFFIC         => [
                ReportSummaryValues::TRAFFIC_TYPE_EVENT
            ],
        ];
    }
}
