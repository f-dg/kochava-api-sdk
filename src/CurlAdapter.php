<?php

namespace KochavaReporting;

use KochavaReporting\AbstractAdapter;

class CurlAdapter extends AbstractAdapter
{
    /**
     * HTTP POST request
     */
    public function postRequest($endpoint, array $params)
    {
        $params = json_encode($params);

        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-length: ' . strlen($params),
        ]);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result, 1);
    }

    /**
     * HTTP GET request
     */
    public function getRequest($endpoint, array $params)
    {
    }
}

