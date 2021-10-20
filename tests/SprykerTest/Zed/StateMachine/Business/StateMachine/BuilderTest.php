<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\StateMachine\Business\StateMachine;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Spryker\Zed\StateMachine\Business\Exception\StateMachineException;
use Spryker\Zed\StateMachine\Business\Process\Event;
use Spryker\Zed\StateMachine\Business\Process\Process;
use Spryker\Zed\StateMachine\Business\Process\State;
use Spryker\Zed\StateMachine\Business\Process\Transition;
use Spryker\Zed\StateMachine\Business\StateMachine\Builder;
use Spryker\Zed\StateMachine\StateMachineConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group StateMachine
 * @group Business
 * @group StateMachine
 * @group BuilderTest
 * Add your own group annotations below this line
 */
class BuilderTest extends Unit
{
    /**
     * @return void
     */
    public function testCreateProcessShouldReturnProcessInstance(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertInstanceOf(Process::class, $process);
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldIncludeAllStatesFromXml(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertCount(14, $process->getStates());
        $this->assertInstanceOf(State::class, $process->getStates()['completed']);
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldIncludeAllTransitions(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertCount(22, $process->getTransitions());
        $this->assertInstanceOf(Transition::class, $process->getTransitions()[0]);
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldIncludeAllSubProcesses(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertCount(2, $process->getSubProcesses());
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldFlagMainProcess(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $this->assertTrue($process->getIsMain());
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldThrowExceptionWhenStateMachineXmlFileNotFound(): void
    {
        // Arrange
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $stateMachineProcessTransfer->setStateMachineName('Random');

        // Assert
        $this->expectException(StateMachineException::class);

        // Act
        $builder->createProcess($stateMachineProcessTransfer);
    }

    /**
     * @return void
     */
    public function testCreateProcessShouldThrowExceptionWhenProcessXmlFileNotFound(): void
    {
        // Arrange
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName('Random');

        // Assert
        $this->expectException(StateMachineException::class);

        // Act
        $builder->createProcess($stateMachineProcessTransfer);
    }

    /**
     * @return void
     */
    public function testSubProcessPrefixIsApplied(): void
    {
        $builder = $this->createBuilder();
        $stateMachineProcessTransfer = $this->createStateMachineProcessTransfer();
        $process = $builder->createProcess($stateMachineProcessTransfer);

        $manualEventsBySource = $process->getManuallyExecutableEventsBySource();

        $this->assertSame('Foo 1 - action', $manualEventsBySource['Foo 1 - sub process state'][0]);
        $this->assertSame('Leave Sub-process 2', $manualEventsBySource['Foo 1 - done'][0]);
    }

    /**
     * @return \Spryker\Zed\StateMachine\Business\StateMachine\Builder
     */
    protected function createBuilder(): Builder
    {
        return new Builder(
            $this->createEvent(),
            $this->createState(),
            $this->createTransition(),
            $this->createProcess(),
            $this->createStateMachineConfig(),
        );
    }

    /**
     * @return \Spryker\Zed\StateMachine\Business\Process\Event
     */
    protected function createEvent(): Event
    {
        return new Event();
    }

    /**
     * @return \Spryker\Zed\StateMachine\Business\Process\State
     */
    protected function createState(): State
    {
        return new State();
    }

    /**
     * @return \Spryker\Zed\StateMachine\Business\Process\Transition
     */
    protected function createTransition(): Transition
    {
        return new Transition();
    }

    /**
     * @return \Spryker\Zed\StateMachine\Business\Process\Process
     */
    protected function createProcess(): Process
    {
        return new Process();
    }

    /**
     * @return \Spryker\Zed\StateMachine\StateMachineConfig
     */
    protected function createStateMachineConfig(): StateMachineConfig
    {
        $stateMachineConfigMock = $this->getMockBuilder(StateMachineConfig::class)->getMock();

        $pathToStateMachineFixtures = realpath(__DIR__ . '/../../_support/Fixtures');
        $stateMachineConfigMock->method('getPathToStateMachineXmlFiles')->willReturn($pathToStateMachineFixtures);
        $stateMachineConfigMock->method('getSubProcessPrefixDelimiter')->willReturn(' - ');

        return $stateMachineConfigMock;
    }

    /**
     * @return \Generated\Shared\Transfer\StateMachineProcessTransfer
     */
    protected function createStateMachineProcessTransfer(): StateMachineProcessTransfer
    {
        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName('TestProcess');
        $stateMachineProcessTransfer->setStateMachineName('TestingSm');

        return $stateMachineProcessTransfer;
    }
}
