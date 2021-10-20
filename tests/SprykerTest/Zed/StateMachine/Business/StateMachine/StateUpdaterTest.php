<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\StateMachine\Business\StateMachine;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateHistory;
use Propel\Runtime\Connection\ConnectionInterface;
use Spryker\Zed\StateMachine\Business\Process\Process;
use Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\StateUpdater;
use Spryker\Zed\StateMachine\Business\StateMachine\TimeoutInterface;
use Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface;
use SprykerTest\Zed\StateMachine\Mocks\StateMachineMocks;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group StateMachine
 * @group Business
 * @group StateMachine
 * @group StateUpdaterTest
 * Add your own group annotations below this line
 */
class StateUpdaterTest extends StateMachineMocks
{
    /**
     * @var string
     */
    public const TEST_STATE_MACHINE_NAME = 'test state machine name';

    /**
     * @return void
     */
    public function testStateUpdaterShouldUpdateStateInTransaction(): void
    {
        $stateUpdater = $this->createStateUpdater();

        $stateUpdater->updateStateMachineItemState(
            [$this->createStateMachineItems()[0]],
            $this->createProcesses(),
            $this->createSourceStateBuffer(),
        );
    }

    /**
     * @return void
     */
    public function testStateUpdaterShouldTriggerHandlerWhenStateChanged(): void
    {
        $stateMachineHandlerResolverMock = $this->createHandlerResolverMock();

        $handlerMock = $this->createStateMachineHandlerMock();
        $handlerMock->expects($this->once())
            ->method('itemStateUpdated')
            ->with($this->isInstanceOf(StateMachineItemTransfer::class));

        $stateMachineHandlerResolverMock->method('get')->willReturn($handlerMock);

        $stateUpdater = $this->createStateUpdater(
            null,
            $stateMachineHandlerResolverMock,
        );

        $stateUpdater->updateStateMachineItemState(
            $this->createStateMachineItems(),
            $this->createProcesses(),
            $this->createSourceStateBuffer(),
        );
    }

    /**
     * @return void
     */
    public function testStateUpdaterShouldUpdateTimeoutsWhenStateChanged(): void
    {
        $timeoutMock = $this->createTimeoutMock();

        $timeoutMock->expects($this->once())->method('dropOldTimeout');
        $timeoutMock->expects($this->once())->method('setNewTimeout');

        $stateUpdater = $this->createStateUpdater(
            $timeoutMock,
        );

        $stateUpdater->updateStateMachineItemState(
            $this->createStateMachineItems(),
            $this->createProcesses(),
            $this->createSourceStateBuffer(),
        );
    }

    /**
     * @return void
     */
    public function testStateMachineUpdaterShouldPersistStateHistory(): void
    {
        $persistenceMock = $this->createPersistenceMock();
        $persistenceMock->expects($this->once())->method('saveItemStateHistory')->with(
            $this->isInstanceOf(StateMachineItemTransfer::class),
        );

        $stateUpdater = $this->createStateUpdater(
            null,
            null,
            $persistenceMock,
        );

        $stateUpdater->updateStateMachineItemState(
            $this->createStateMachineItems(),
            $this->createProcesses(),
            $this->createSourceStateBuffer(),
        );
    }

    /**
     * @return array<\Generated\Shared\Transfer\StateMachineItemTransfer>
     */
    protected function createStateMachineItems(): array
    {
        $items = [];

        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setProcessName('Test');
        $stateMachineItemTransfer->setIdentifier(1);
        $stateMachineItemTransfer->setStateName('target');
        $stateMachineItemTransfer->setStateMachineName(static::TEST_STATE_MACHINE_NAME);
        $items[] = $stateMachineItemTransfer;

        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setProcessName('Test');
        $stateMachineItemTransfer->setIdentifier(2);
        $stateMachineItemTransfer->setStateName('target');
        $stateMachineItemTransfer->setStateMachineName(static::TEST_STATE_MACHINE_NAME);
        $items[] = $stateMachineItemTransfer;

        return $items;
    }

    /**
     * @return array<\Spryker\Zed\StateMachine\Business\Process\Process>
     */
    protected function createProcesses(): array
    {
        $processes = [];

        $process = new Process();

        $processes['Test'] = $process;

        return $processes;
    }

    /**
     * @return array<\Spryker\Zed\StateMachine\Business\Process\State>
     */
    protected function createSourceStateBuffer(): array
    {
        $sourceStates = [];

        $sourceStates[1] = 'target';
        $sourceStates[2] = 'updated';

        return $sourceStates;
    }

    /**
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\TimeoutInterface|null $timeoutMock
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface|null $handlerResolverMock
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface|null $stateMachinePersistenceMock
     * @param \Propel\Runtime\Connection\ConnectionInterface|null $propelConnectionMock
     *
     * @return \Spryker\Zed\StateMachine\Business\StateMachine\StateUpdater
     */
    protected function createStateUpdater(
        ?TimeoutInterface $timeoutMock = null,
        ?HandlerResolverInterface $handlerResolverMock = null,
        ?PersistenceInterface $stateMachinePersistenceMock = null,
        ?ConnectionInterface $propelConnectionMock = null
    ): StateUpdater {
        if ($timeoutMock === null) {
            $timeoutMock = $this->createTimeoutMock();
        }

        if ($handlerResolverMock === null) {
            $handlerResolverMock = $this->createHandlerResolverMock();

            $handlerMock = $this->createStateMachineHandlerMock();
            $handlerResolverMock->method('get')->willReturn($handlerMock);
        }

        if ($stateMachinePersistenceMock === null) {
            $stateMachinePersistenceMock = $this->createStateMachinePersistenceMock();
        }

        if ($propelConnectionMock === null) {
            $propelConnectionMock = $this->createPropelConnectionMock();
        }

        $queryContainerMock = $this->createQueryContainerMock();
        $queryContainerMock->method('getConnection')
            ->willReturn($propelConnectionMock);

        $stateMachineMachineHistoryQueryMock = $this->createStateMachineHistoryQueryMock();
        $queryContainerMock->method('queryLastHistoryItem')
            ->willReturn($stateMachineMachineHistoryQueryMock);

        return new StateUpdater(
            $timeoutMock,
            $handlerResolverMock,
            $stateMachinePersistenceMock,
            $queryContainerMock,
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface
     */
    protected function createQueryContainerMock(): StateMachineQueryContainerInterface
    {
        return $this->getMockBuilder(StateMachineQueryContainerInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateHistory
     */
    protected function createStateMachineHistoryQueryMock(): SpyStateMachineItemStateHistory
    {
        return $this->getMockBuilder(SpyStateMachineItemStateHistory::class)->getMock();
    }
}
