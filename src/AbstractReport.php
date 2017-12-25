<?php

namespace KochavaReporting;

class ReportException extends \Exception {}

use KochavaReporting\AbstractReportValues;

abstract class AbstractReport 
{
    const BASE_URL = 'https://reporting.api.kochava.com';
    const API_PING_FREQUENCY = 10; //seconds

    const ENDPOINT_REPORT_STATUS = 'progress';
    const ENDPOINT_APPLICATIONS = 'getapps';
    const ENDPOINT_REPORTCOLUMNS = 'reportcolumns';

    const DEFAULT_TIME_ZONE = 'UTC';
    const DEFAULT_API_VERSION = 'v1.3';

    const DELIVERY_FORMAT_JSON = 'json';
    const DELIVERY_FORMAT_CSV  = 'csv';

    const ERROR_MSG = 'response without any errors';

    protected $_apiKey;
    protected $_appGuid;
    protected $_apiVersion;
    protected $_adapter;
    protected $_deliveryFormat; 
    protected $_eachReportAsArray;

    abstract protected function addReportToQueue(array $params);

    public function __construct(
        AbstractAdapter $adapter, 
        $apiKey, 
        $appGuid = null, 
        $apiVersion = null, 
        $eachReportAsArray = true
    )
    {
        $this->_adapter = $adapter;
        $this->_apiKey = $apiKey;
        $this->_appGuid = $appGuid;
        $this->_apiVersion = $apiVersion ?: static::DEFAULT_API_VERSION;
        $this->_eachReportAsArray = $eachReportAsArray;
        $this->_deliveryFormat = static::DELIVERY_FORMAT_JSON;
    }

    public function getApplications()
    {
        $endpoint = $this->getEndpoint(static::ENDPOINT_APPLICATIONS);
        $params = [AbstractReportValues::FIELD_API_KEY => $this->_apiKey];

        return $this->_adapter->postRequest($endpoint, $params);
    }

    public function getColumns()
    {
        $endpoint = $this->getEndpoint(static::ENDPOINT_REPORTCOLUMNS);
        $params = [
            AbstractReportValues::FIELD_API_KEY  => $this->_apiKey,
            AbstractReportValues::FIELD_APP_GUID => $this->_appGuid,
            'report' => 'event'
        ];
        return $this->_adapter->postRequest($endpoint, $params);
    }
    /**
     * @throw ReportException
     */
    public function getReport(array $requestParams)
    {
        $job = $this->addReportToQueue($requestParams);

        if ($job['status'] === 'Error') {
            throw new ReportException($this->getErrorMsg($job));
        }

        $job = $this->obtainReportStatus($job);

        if ($job['status'] !== 'completed' || empty($job['report'])) {
            throw new ReportException($this->getErrorMsg($job));
        }

        $report = $this->fetchReport($job['report']);

        return $report;
    }

    public function getEndpoint($endpoint)
    {
        return static::BASE_URL . '/' . $this->_apiVersion . '/' . $endpoint;
    }

    /**
     * @throw ReportException
     */
    protected function obtainReportStatus(array $job)
    {
        $endpoint = $this->getEndpoint(static::ENDPOINT_REPORT_STATUS);
        $params = [
            AbstractReportValues::FIELD_API_KEY  => $this->_apiKey,
            AbstractReportValues::FIELD_APP_GUID => $this->_appGuid,
            AbstractReportValues::FIELD_TOKEN    => $job['report_token'],
        ];

        do {
            $job = $this->_adapter->postRequest($endpoint, $params);
            if (strtolower($job['status']) == 'error') {
                throw new ReportException($this->getErrorMsg($job));
            }
            sleep(static::API_PING_FREQUENCY);
        } while (!in_array(strtolower($job['status']), ['completed', 'error']));

        return $job;
    }

    /**
     * @throw ReportException
     */
    protected function fetchReport($pathToReport)
    {
        $method = 'readReportAs' . strtolower($this->_deliveryFormat);

        if (!method_exists($this, $method)) {
            throw new ReportException('Method "' . $method . '" not exists');
        }

        return $this->$method($pathToReport) ?: [];
    }

    /**
     * there is a problem - sometimes values in the report 
     * have a nested arrays or objects
     * "fgetcsv" read such file in wrong way, 
     * that's why is recommended to use
     * json report or correctly implement this method
     */
    protected function readReportAsCSV($reportPath)
    {
        throw new ReportException('Method have to be implemented');
    }

    protected function readReportAsJSON($reportPath)
    {
        return json_decode(file_get_contents($reportPath), $this->_eachReportAsArray);
    }

    protected function getErrorMsg(array $job)
    {
        $errorMsg = isset($job['error']) 
            ? $job['error']
            : static::ERROR_MSG;
        return $errorMsg;
    }

    public function getDeliveryFormat()
    {
        return $this->_deliveryFormat;
    }
}

