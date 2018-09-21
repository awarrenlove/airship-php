<?php
namespace Airship;

use Airship\Client\ClientInterface;


class Flag
{
    public $flagName;

    /**
     * @var Airship
     */
    private $delegate;

    public function __construct($flagName, Airship $delegate)
    {
        $this->flagName = $flagName;
        $this->delegate = $delegate;
    }

    public function getTreatment($entity)
    {
        return $this->delegate->getTreatment($this, $entity);
    }

    public function getPayload($entity)
    {
        return $this->delegate->getPayload($this, $entity);
    }

    public function isEligible($entity)
    {
        return $this->delegate->isEligible($this, $entity);
    }

    public function isEnabled($entity)
    {
        return $this->delegate->isEnabled($this, $entity);
    }
}

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

    public function __toString()
    {
        return '[Airship object]';
    }

    public function flag($flagName)
    {
        return new Flag($flagName, $this);
    }

    private function getObjectValues($flag, $entity)
    {
        if ($entity instanceof Target) {
            $entity = $entity->toArray();
        }

        $objectValues = $this->client->sendRequest([
            'flag' => $flag->flagName,
            'entity' => $entity,
        ]);

        return $objectValues;
    }

    public function getTreatment($flag, $entity)
    {
        $objectValues = $this->getObjectValues($flag, $entity);
        if (isset($objectValues['treatment'])) {
            return $objectValues['treatment'];
        }

        return 'off';
    }

    public function getPayload($flag, $entity)
    {
        $objectValues = $this->getObjectValues($flag, $entity);
        if (isset($objectValues['payload'])) {
            return $objectValues['payload'];
        }

        return NULL;
    }

    public function isEligible($flag, $entity)
    {
        $objectValues = $this->getObjectValues($flag, $entity);
        if (isset($objectValues['isEligible'])) {
            return $objectValues['isEligible'];
        }

        return false;
    }

    public function isEnabled($flag, $entity)
    {
        $objectValues = $this->getObjectValues($flag, $entity);
        if (isset($objectValues['isEnabled'])) {
            return $objectValues['isEnabled'];
        }

        return false;
    }
}
