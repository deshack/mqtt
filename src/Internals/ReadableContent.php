<?php

declare(strict_types=1);

namespace unreal4u\MQTT\Internals;

use unreal4u\MQTT\Exceptions\InvalidResponseType;

/**
 * Trait ReadableContent
 * @package unreal4u\MQTT\Internals
 */
trait ReadableContent
{
    /**
     * Length of variable header
     * @var int
     */
    protected $variableHeaderSize = 0;

    /**
     * @param string $rawMQTTHeaders
     * @param ClientInterface $client
     * @return bool
     * @throws \unreal4u\MQTT\Exceptions\InvalidResponseType
     */
    final public function instantiateObject(string $rawMQTTHeaders, ClientInterface $client): bool
    {
        //var_dump(base64_encode($rawMQTTHeaders)); // Make it a bit easier to create unit tests
        $this->checkControlPacketValue(\ord($rawMQTTHeaders[0]) >> 4);
        $this->fillObject($rawMQTTHeaders, $client);

        return true;
    }

    /**
     * Checks whether the control packet corresponds to this object
     *
     * @param int $controlPacketValue
     * @return bool
     * @throws \unreal4u\MQTT\Exceptions\InvalidResponseType
     */
    private function checkControlPacketValue(int $controlPacketValue): bool
    {
        // Check whether the first byte corresponds to the expected control packet value
        if (static::CONTROL_PACKET_VALUE !== $controlPacketValue) {
            throw new InvalidResponseType(sprintf(
                'Value of received value does not correspond to response (Expected: %d, Actual: %d)',
                static::CONTROL_PACKET_VALUE,
                $controlPacketValue
            ));
        }

        return true;
    }

    /**
     * All classes must implement how to handle the object filling
     * @param string $rawMQTTHeaders
     * @param ClientInterface $client
     * @return ReadableContentInterface
     */
    abstract public function fillObject(string $rawMQTTHeaders, ClientInterface $client): ReadableContentInterface;

    /**
     * Any class can overwrite the default behaviour
     * @param ClientInterface $client
     * @param WritableContentInterface $originalRequest
     * @return bool
     */
    public function performSpecialActions(ClientInterface $client, WritableContentInterface $originalRequest): bool
    {
        return false;
    }
}
