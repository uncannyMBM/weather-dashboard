<?php
return [
    'pm25' => [
        [
            "bpLo" => 0,
            "bpHi" => 12.0,
            "iLo" => 0,
            "iHi" => 50,
            "info" => "Good"
        ], [
            "bpLo" => 12.1,
            "bpHi" => 35.4,
            "iLo" => 51,
            "iHi" => 100,
            "info" => "Moderate"
        ], [
            "bpLo" => 35.5,
            "bpHi" => 55.4,
            "iLo" => 101,
            "iHi" => 150,
            "info" => "Unhealthy For Sensitive Groups"
        ], [
            "bpLo" => 55.5,
            "bpHi" => 150.4,
            "iLo" => 151,
            "iHi" => 200,
            "info" => "Unhealthy"
        ], [
            "bpLo" => 150.5,
            "bpHi" => 250.4,
            "iLo" => 201,
            "iHi" => 300,
            "info" => "Very Unhealthy"
        ], [
            "bpLo" => 250.5,
            "bpHi" => 350.4,
            "iLo" => 301,
            "iHi" => 400,
            "info" => "Hazardous"

        ], [
            "bpLo" => 350.5,
            "bpHi" => 500,
            "iLo" => 401,
            "iHi" => 500,
            "info" => "Hazardous"
        ]
    ],
    'pm10' => [
        [
            "bpLo" => 0,
            "bpHi" => 54,
            "iLo" => 0,
            "iHi" => 50,
            "info" => "Good"
        ], [
            "bpLo" => 55,
            "bpHi" => 154,
            "iLo" => 51,
            "iHi" => 100,
            "info" => "Moderate"
        ], [
            "bpLo" => 155,
            "bpHi" => 254,
            "iLo" => 101,
            "iHi" => 150,
            "info" => "Unhealthy For Sensitive Groups"
        ], [
            "bpLo" => 255,
            "bpHi" => 354,
            "iLo" => 151,
            "iHi" => 200,
            "info" => "Unhealthy"
        ], [
            "bpLo" => 355,
            "bpHi" => 424,
            "iLo" => 201,
            "iHi" => 300,
            "info" => "Very Unhealthy"
        ], [
            "bpLo" => 425,
            "bpHi" => 504,
            "iLo" => 301,
            "iHi" => 400,
            "info" => "Hazardous"
        ], [
            "bpLo" => 505,
            "bpHi" => 604,
            "iLo" => 401,
            "iHi" => 500,
            "info" => "Hazardous"
        ]
    ]
];