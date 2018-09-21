<?php

namespace Airship\Client;

interface ClientInterface
{
    const PLATFORM = 'php';
    const VERSION = '2.0.0';

    const SERVER_URL = 'http://localhost:5000';
    const OBJECT_GATE_VALUES_ENDPOINT = '/v2/object-values/';

    /**
     * @param array $data
     *
     * @return array
     */
    public function sendRequest($data);
}
