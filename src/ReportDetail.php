<?php

namespace KochavaReporting;

use KochavaReporting\AbstractReport,
    KochavaReporting\AbstractReportValues,
    KochavaReporting\ReportDetailValues;

class ReportDetail extends AbstractReport
{
    const ENDPOINT = 'detail';

    protected function addReportToQueue(array $requestParams)
    {
        $endpoint = $this->getEndpoint(static::ENDPOINT);
        $requestParams = array_merge(
            $this->getDefaultParams(),
            $requestParams
        );

        $this->_deliveryFormat = $requestParams[ReportDetailValues::FIELD_DELIVERY_FORMAT];

        $job = $this->_adapter->postRequest($endpoint, $requestParams);
        return $job;
    }

    protected function getDefaultParams()
    {
        return [
            AbstractReportValues::FIELD_API_KEY       => $this->_apiKey,
            AbstractReportValues::FIELD_APP_GUID      => $this->_appGuid,
            ReportDetailValues::FIELD_DELIVERY_FORMAT => static::DELIVERY_FORMAT_JSON,
            ReportDetailValues::FIELD_TIME_ZONE       => static::DEFAULT_TIME_ZONE,
            ReportDetailValues::FIELD_TRAFFIC         => [
                ReportDetailValues::TRAFFIC_TYPE_EVENT
            ],
        ];
    }
}

