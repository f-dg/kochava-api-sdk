<?php

namespace KochavaReporting;

class ReportDetailValues extends AbstractReportValues 
{
    const FIELD_TRAFFIC_INCLUDING       = 'traffic_including';

    const TRAFFIC_TYPE_CLICK            = 'click';
    const TRAFFIC_TYPE_COST             = 'cost';
    const TRAFFIC_TYPE_IMPRESSION       = 'impression';
    const TRAFFIC_TYPE_INSTALL          = 'install';
    const TRAFFIC_TYPE_EVENT            = 'event';
    const TRAFFIC_TYPE_REENGAGEMENT     = 'reengagement';
    const TRAFFIC_TYPE_TRAFFICVERIF     = 'trafficverif';
    const TRAFFIC_TYPE_INFLUENCER_IMP   = 'influencer_imp';
    const TRAFFIC_TYPE_INFLUENCER_CLICK = 'influencer_click';
    const TRAFFIC_TYPE_FRACTIONAL       = 'fractional';

    const TRAFFIC_INCLUDING_CUSTOM_PARAMETERS    = 'custom_parameters';
    const TRAFFIC_INCLUDING_IDENTITYLINK         = 'identitylink';
    const TRAFFIC_INCLUDING_TRAFFIC_VERIFICATION = 'traffic_verification';
    const TRAFFIC_INCLUDING_UNATTRIBUTED_TRAFFIC = 'unattributed_traffic';
    const TRAFFIC_INCLUDING_UNATTRIBUTED_ONLY    = 'unattributed_only';
}

