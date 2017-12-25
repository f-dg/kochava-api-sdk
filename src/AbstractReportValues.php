<?php

namespace KochavaReporting;

abstract class AbstractReportValues 
{
    const FIELD_API_KEY            = 'api_key';
    const FIELD_APP_GUID           = 'app_guid';
    const FIELD_TIME_START         = 'time_start';
    const FIELD_TIME_END           = 'time_end';
    const FIELD_TRAFFIC            = 'traffic'; 
    const FIELD_TIME_ZONE          = 'time_zone';
    const FIELD_TRAFFIC_FILTERING  = 'traffic_filtering';
    const FIELD_CUSTOM_REPORT_NAME = 'custom_report_name';
    const FIELD_DELIVERY_FORMAT    = 'delivery_format';
    const FIELD_DELIVERY_METHOD    = 'delivery_method';
    const FIELD_NOTIFY             = 'notify';
    const FIELD_TOKEN              = 'token';
    const FIELD_MAX_TOKENS         = 'max_tokens';

    const FIELD_TRAFFIC_FILTERING_NETWORK = 'network';
    const FACEBOOK_NETRWORK_ID = '65';

    public static function getTrafficList()
    {
        $self = new \ReflectionClass(get_called_class());
        $constants = $self->getConstants();
        $toReturn = [];
        foreach ($constants as $name => $const) {
            if (stripos($name, 'TRAFFIC_TYPE_') === 0) {
                $toReturn[] = $const;
            }
        }
        return $toReturn;
    }
}

