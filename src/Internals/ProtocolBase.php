<?php

declare(strict_types=1);

namespace unreal4u\MQTT\Internals;

use Psr\Log\LoggerInterface;
use unreal4u\MQTT\DummyLogger;

abstract class ProtocolBase
{
    /**
     * The actual logger object
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Base constructor for all protocol stuff
     * @param LoggerInterface|null $logger
     */
    final public function __construct(LoggerInterface $logger = null)
    {
        if ($logger === null) {
            $logger = new DummyLogger();
        }

        $this->logger = $logger;
    }
}