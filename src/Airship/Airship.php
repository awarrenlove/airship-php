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

    private $localObjectsCache = [];

    private $localGateValuesCache = [];

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

    private function getGateValues($obj)
    {
        $uniqueId = $this->getUniqueId($obj);

        if (isset($this->localObjectsCache[$uniqueId])) {
            $storedObj = $this->localObjectsCache[$uniqueId];

            if ($obj === $storedObj) {
                return $this->localGateValuesCache[$uniqueId];
            }
        }

        $gateValues = $this->client->sendRequest($obj);

        $this->localObjectsCache[$uniqueId] = $obj;
        $this->localGateValuesCache[$uniqueId] = $gateValues;

        return $gateValues;
    }

    public function isEnabled($controlName, $obj, $default = false)
    {
        $gateValues = $this->getGateValues($obj);
        if (isset($gateValues[$controlName])) {
            return $gateValues[$controlName]['is_enabled'];
        } else {
            return $default;
        }
    }

    public function getVariation($controlName, $obj, $default = null)
    {
        $gateValues = $this->getGateValues($obj);
        if (isset($gateValues[$controlName]) && isset($gateValues[$controlName]['variation'])) {
            return $gateValues[$controlName]['variation'];
        } else {
            return $default;
        }
    }

    public function isEligible($controlName, $obj, $default = false)
    {
        $gateValues = $this->getGateValues($obj);
        if (isset($gateValues[$controlName])) {
            return $gateValues[$controlName]['is_eligible'];
        } else {
            return $default;
        }
    }
}
