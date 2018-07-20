<?php

namespace Airship\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;

class GuzzleClient implements ClientInterface
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $envKey;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param string $apiKey
     * @param string $envKey
     * @param array  $config An array of Guzzle Client options for configuring the client
     */
    public function __construct($apiKey, $envKey, array $config = [])
    {
        $this->apiKey = $apiKey;
        $this->envKey = $envKey;

        $this->client = new Client(array_merge_recursive([
            'base_uri' => self::SERVER_URL,
            'headers' => [
                'Content-Type'  => 'application/json',
                'Api-Key'       => $this->apiKey,
                'Accept'        => 'application/json',
                'SDK-Version'   => self::PLATFORM . ':' . self::VERSION
            ],
            'timeout' => 60,
            'connect_timeout' => 60
        ], $config));
    }

    public function sendRequest($obj)
    {
        $response = null;

        try {
            $options['body'] = json_encode($obj);
            $response = $this->client->request(
                'POST',
                self::OBJECT_GATE_VALUES_ENDPOINT . $this->envKey,
                $options
            );
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode === 403) {
                throw new \Exception('Invalid Airship instance - check API Key and Env Key.');
            } else {
                throw $e;
            }
        } catch (BadResponseException $e) {
            throw new \Exception('Bad response - make sure object conforms to valid shape.');
        }

        return json_decode((string) $response->getBody(), true);
    }
}
