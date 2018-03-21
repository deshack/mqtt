<?php

declare(strict_types=1);

namespace tests\unreal4u\MQTT;

use PHPUnit\Framework\TestCase;
use tests\unreal4u\MQTT\Mocks\ClientMock;
use unreal4u\MQTT\Exceptions\Connect\BadUsernameOrPassword;
use unreal4u\MQTT\Exceptions\Connect\GenericError;
use unreal4u\MQTT\Exceptions\Connect\IdentifierRejected;
use unreal4u\MQTT\Exceptions\Connect\NotAuthorized;
use unreal4u\MQTT\Exceptions\Connect\ServerUnavailable;
use unreal4u\MQTT\Exceptions\Connect\UnacceptableProtocolVersion;
use unreal4u\MQTT\Protocol\ConnAck;
use unreal4u\MQTT\Protocol\Connect;

class ConnAckTest extends TestCase
{
    /**
     * @var ConnAck
     */
    private $connAck;

    protected function setUp()
    {
        $this->connAck = new ConnAck();
        parent::setUp();
    }

    public function test_getOriginControlPacketValue()
    {
        $this->assertSame(Connect::getControlPacketValue(), $this->connAck->getOriginControlPacket());
    }

    /**
     * May seem like a useless test, but if no exception is thrown, the object itself will be returned.
     *
     * This test will assert that no exception is actually being thrown.
     */
    public function test_emulateSuccessfulConnection()
    {
        $clientMock = new ClientMock();

        $this->assertInstanceOf(
            ConnAck::class,
            $this->connAck->fillObject(base64_decode('IAIBAA=='), $clientMock)
        );

        $this->connAck->performSpecialActions($clientMock, new Connect());
        $this->assertTrue($clientMock->setConnectedWasCalled());
        $this->assertTrue($clientMock->updateLastCommunicationWasCalled());
    }

    public function provider_TestExceptions(): array
    {
        $mapValues[] = [UnacceptableProtocolVersion::class, 'IAIAAQ=='];
        $mapValues[] = [IdentifierRejected::class, 'IAIAAg=='];
        $mapValues[] = [ServerUnavailable::class, 'IAIAAw=='];
        $mapValues[] = [BadUsernameOrPassword::class, 'IAIABA=='];
        $mapValues[] = [NotAuthorized::class, 'IAIABQ=='];
        // Should never occur, but test it either way
        $mapValues[] = [GenericError::class, 'IAIAfw=='];

        return $mapValues;
    }

    /**
     * @dataProvider provider_TestExceptions
     *
     * @param string $expectedException
     * @param string $encodedResponse
     */
    public function test_exception(string $expectedException, string $encodedResponse)
    {
        $this->expectException($expectedException);
        $this->connAck->fillObject(base64_decode($encodedResponse), new ClientMock());
    }
}
