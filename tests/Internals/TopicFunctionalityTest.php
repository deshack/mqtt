<?php

declare(strict_types=1);

namespace tests\unreal4u\MQTT\Internals;

use PHPUnit\Framework\TestCase;
use unreal4u\MQTT\DataTypes\TopicFilter;
use unreal4u\MQTT\Exceptions\MustContainTopic;
use unreal4u\MQTT\Internals\TopicFilterFunctionality;

class TopicFunctionalityTest extends TestCase
{
    use TopicFilterFunctionality;

    protected function setUp()
    {
        $this->topics = new \SplQueue();
        parent::setUp();
    }

    public function test_zeroTopics()
    {
        $this->assertSame(0, $this->getNumberOfTopics());
    }

    public function test_missingTopic()
    {
        $this->expectException(MustContainTopic::class);
        foreach ($this->getTopics() as $topic) {
            $this->assertTrue(true);
        }
    }

    public function test_addOneTopic()
    {
        $this->addTopics(new TopicFilter('test'));
        $this->assertSame(1, $this->getNumberOfTopics());
    }

    public function test_addMultipleTopicsInOneGo()
    {
        $this->addTopics(new TopicFilter('test_a'), new TopicFilter('test_b'));
        $this->assertSame(2, $this->getNumberOfTopics());
    }

    public function test_addMultipleTopicsInMultipleStages()
    {
        $this->addTopics(new TopicFilter('test_a'));
        $this->assertSame(1, $this->getNumberOfTopics());

        $this->addTopics(new TopicFilter('test_b'));
        $this->assertSame(2, $this->getNumberOfTopics());
    }

    public function test_addSameTopicMoreThanOnce()
    {
        $this->addTopics(new TopicFilter('test_a'));
        $this->assertSame(1, $this->getNumberOfTopics());

        $this->addTopics(new TopicFilter('test_a'));
        $this->assertSame(1, $this->getNumberOfTopics());
    }
}
