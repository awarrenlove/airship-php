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

class Airship
{
    const PLATFORM = 'php';
    const VERSION = '0.1.0';

    const SERVER_URL = 'https://api.airshiphq.com';
    const OBJECT_GATE_VALUES_ENDPOINT = '/v1/object-gate-values/';

    private $apiKey;
    private $envKey;
    private $requestOptions = null;
    private $localCache = array();

    public function __construct($apiKey, $envKey)
    {
        $this->apiKey = $apiKey;
        $this->envKey = $envKey;

        $this->requestOptions = [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Api-Key'       => $this->apiKey,
                'Accept'        => 'application/json',
                'SDK-Version'   => self::PLATFORM . ':' . self::VERSION
            ],
            'timeout' => 60,
            'connect_timeout' => 60
        ];
    }

    public function __destruct()
    {
        $this->flush();
    }

    public function flush()
    {
        // Do nothing for now.
    }

    public function __toString()
    {
        return '[Airship object]';
    }

    private function getUniqueId($obj)
    {
        $type = '';
        $id = $obj['id'];

        if (isset($obj['type'])) {
            $type = $obj['type'];
        } else {
            $type = 'User';
        }

        $groupType = '';
        $groupId = '';

        if (isset($obj['group'])) {
            $group = $obj['group'];
            $groupId = $group['id'];

            if (isset($group['type'])) {
                $groupType = $group['type'];
            } else {
                $groupType = $type . 'Group';
            }
        }

        $finalId = $type . '_' . $id;

        if ($groupId !== '') {
            $finalId = $finalId . ':' . $groupType . '_' . $groupId;
        }

        return $finalId;
    }

    private function getGateValuesMap($obj)
    {
        $client = new Client(['base_uri' => self::SERVER_URL]);
        $response = null;
        try {
            $options = $this->requestOptions;
            $options['body'] = json_encode($obj);
            $response = $client->request('POST', self::OBJECT_GATE_VALUES_ENDPOINT . $this->envKey, $options);
        } catch (BadResponseException $e) {
            throw new \Exception('Bad response - make sure object conforms to valid shape.');
        } catch (ClientException $e) {
            $status_code = $e->getResponse()->getStatusCode();
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

    public function isEnabled($controlName, $obj, $default = false)
    {
        $objectGateValuesMap = $this->getGateValuesMap($obj);
        if (isset($objectGateValuesMap[$controlName])) {
            return $objectGateValuesMap[$controlName]['is_enabled'];
        } else {
            return $default;
        }
    }

    public function getVariation($controlName, $obj, $default = null)
    {
        $objectGateValuesMap = $this->getGateValuesMap($obj);
        if (isset($objectGateValuesMap[$controlName])) {
            return $objectGateValuesMap[$controlName]['variation'];
        } else {
            return $default;
        }
    }

    public function isEligible($controlName, $obj, $default = false)
    {
        $objectGateValuesMap = $this->getGateValuesMap($obj);
        if (isset($objectGateValuesMap[$controlName])) {
            return $objectGateValuesMap[$controlName]['is_eligible'];
        } else {
            return $default;
        }
    }
}
