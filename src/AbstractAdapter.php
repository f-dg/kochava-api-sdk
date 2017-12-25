<?php

namespace KochavaReporting;

abstract class AbstractAdapter
{
    abstract public function postRequest($endpoint, array $params);
    abstract public function getRequest($endpoint, array $params);
}

