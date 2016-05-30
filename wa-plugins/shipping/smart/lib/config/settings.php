<?php
return array(
    'rate_zone'        => array(
        'value' => array(
            'country' => '',
            'region'  => '',
            'city'    => ''
        ),
    ),
    'currency'         => array(
        'value' => 'RUB',
    ),
    'weight_dimension' => array(
        'value' => 'kg'
    ),
    'delivery_time'    => array(
        'value' => '+1 day',
        'title' => /*_wp*/('Estimated delivery time'),
        'description' => /*_wp*/('Average order transit time. Estimated delivery date will be calculated automatically and shown to customer.'),
        'control_type' => waHtmlControl::RADIOGROUP,
        'options' => array(
            '+3 hour' => /*_wp*/('Same day'),
            '+1 day'  => /*_wp*/('Next day'),
            '+2 day, +3 day' => /*_wp*/('2-3 days'),
            '+1 week' => /*_wp*/('1 week'),
            ''        => /*_wp*/('Undefined')
        ),
    ),
    'currency_round'    => array(
        'value' => 0
    ),
    'map'               => array(
        'value' => array(
            'center'            => '55.73, 37.75',
            'route_start_point' => '55.753559, 37.609218',
            'route_max_length'  => 400,
            'zoom'              => 9,
            'width'             => 750,
            'height'            => 400
        ),
    ),
    'zones'             => array()
);
//EOF