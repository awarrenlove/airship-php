<?php
namespace Airship;

use Airship\Client\ClientInterface;

// Please do this!
// function shutdown($airship){
//     $airship->flush();
// }
// register_shutdown_function('shutdown', $airship);

class Airship
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
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

    private function getGateValuesMap($obj)
    {
        // $map = $this->cache->get($obj);
        // if ($map === null) {
        //     $map = $this->client->get($obj);
        // }
        // return $map;
        return $this->client->sendRequest($obj);
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
        if (isset($objectGateValuesMap[$controlName]) && isset($objectGateValuesMap[$controlName]['variation'])) {
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
