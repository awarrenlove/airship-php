<?php

namespace Airship\Client;

interface ClientInterface
{
    const PLATFORM = 'php';
    const VERSION = '1.1.1';

    const SERVER_URL = 'https://api.airshiphq.com';
    const OBJECT_GATE_VALUES_ENDPOINT = '/v1/object-gate-values/';

    /**
     * @param array $obj
     *
     * @return array
     */
    public function sendRequest($obj);
}
