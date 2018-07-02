<?php
namespace Airship;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;

// Please do this!
// function shutdown($airship){
//     $airship->flush();
// }
// register_shutdown_function('shutdown', $airship);

class Airship {

    const SERVER_URL = 'https://api.airshiphq.com';
    const OBJECT_GATE_VALUES_ENDPOINT = '/v1/object-gate-values/';

    private $_api_key;
    private $_env_key;
    private $_request_options = NULL;

    public function __construct($api_key, $env_key) {
        $this->_api_key = $api_key;
        $this->_env_key = $env_key;

        $this->_request_options = [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Api-Key'       => $this->_api_key,
                'Accept'        => 'application/json'
            ],
            'timeout' => 60,
            'connect_timeout' => 60
        ];
    }

    public function __destruct() {
        $this->flush();
    }

    public function flush() {
        // Do nothing for now.
    }

    public function __toString() {
        return '[Airship object]';
    }

    private function _get_gate_values_map($obj) {
        $client = new Client(['base_uri' => self::SERVER_URL]);
        $response = null;
        try {
            $options = $this->_request_options;
            $options['body'] = json_encode($obj);
            $response = $client->request('POST', self::OBJECT_GATE_VALUES_ENDPOINT . $this->_env_key, $options);
        } catch (ClientException $e) {
            $status_code = $e->getResponse()->getStatusCode();
            echo $e;
            if ($status_code === 403) {
                throw new \Exception('Invalid Airship instance - check API Key and Env Key.');
            } else {
                throw $e;
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return json_decode($response->getBody()->getContents(), true);
    }

    public function is_enabled($control_name, $obj, $default=false) {
        $object_gate_values_map = $this->_get_gate_values_map($obj);
        if (isset($object_gate_values_map[$control_name])) {
            return $object_gate_values_map[$control_name]['is_enabled'];
        } else {
            return $default;
        }
    }

    public function get_variation($control_name, $obj, $default=NULL) {
        $object_gate_values_map = $this->_get_gate_values_map($obj);
        if (isset($object_gate_values_map[$control_name])) {
            return $object_gate_values_map[$control_name]['variation'];
        } else {
            return $default;
        }
    }

    public function is_eligible($control_name, $obj, $default=false) {
        $object_gate_values_map = $this->_get_gate_values_map($obj);
        if (isset($object_gate_values_map[$control_name])) {
            return $object_gate_values_map[$control_name]['is_eligible'];
        } else {
            return $default;
        }
    }
}
