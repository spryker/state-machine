<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\StateMachine\Business\StateMachine;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use LogicException;
use Spryker\Zed\StateMachine\Business\Logger\TransitionLogInterface;
use Spryker\Zed\StateMachine\Business\Process\Event;
use Spryker\Zed\StateMachine\Business\Process\Process;
use Spryker\Zed\StateMachine\Business\Process\State;
use Spryker\Zed\StateMachine\Business\Process\Transition;
use Spryker\Zed\StateMachine\Business\StateMachine\ConditionInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\FinderInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\ProcessKeyBuilder;
use Spryker\Zed\StateMachine\Business\StateMachine\StateUpdaterInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\Trigger;
use Spryker\Zed\StateMachine\Dependency\Plugin\CommandByItemsPluginInterface;
use Spryker\Zed\StateMachine\Dependency\Plugin\CommandPluginInterface;
use SprykerTest\Zed\StateMachine\Mocks\StateMachineMocks;
use stdClass;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group StateMachine
 * @group Business
 * @group StateMachine
 * @group TriggerTest
 * Add your own group annotations below this line
 */
class TriggerTest extends StateMachineMocks
{
    /**
     * @var int
     */
    public const ITEM_IDENTIFIER = 1985;

    /**
     * @var string
     */
    public const TESTING_STATE_MACHINE = 'Testing state machine';

    /**
     * @var string
     */
    public const PROCESS_NAME = 'Process';

    /**
     * @var string
     */
    public const INITIAL_STATE = 'new';

    /**
     * @var string
     */
    public const TEST_COMMAND = 'TestCommand';

    /**
     * @var string
     */
    protected const OTHER_TEST_COMMAND = 'OtherTestCommand';

    /**
     * @var string
     */
    protected const OTHER_STATE_NAME = 'other';

    public function testTriggerForNewItemShouldExecutedSMAndPersistNewItem(): void
    {
        $stateMachinePersistenceMock = $this->createPersistenceMock();
        $stateMachinePersistenceMock->expects($this->once())
            ->method('getProcessId')
            ->willReturn(1);

        $stateMachinePersistenceMock->expects($this->once())
            ->method('getInitialStateIdByStateName')
            ->willReturn(1);

        $stateMachinePersistenceMock->expects($this->once())
            ->method('updateStateMachineItemsFromPersistence')
            ->willReturnCallback(
                function ($stateMachineItems) {
                    return $stateMachineItems;
                },
            );

        $finderMock = $this->createFinderMock();
        $finderMock->expects($this->exactly(2))
            ->method('findProcessesForItems')
            ->willReturn($this->createProcesses());

        $finderMock->expects($this->once())
            ->method('findProcessByStateMachineProcess')
            ->willReturn($this->createProcesses()[static::PROCESS_NAME]);

        $finderMock->expects($this->exactly(2))
            ->method('filterItemsWithOnEnterEvent')
            ->willReturnOnConsecutiveCalls(
                $this->createStateMachineItems(),
                [],
            );

        $conditionMock = $this->createTriggerConditionMock();
        $transitionLogMock = $this->createTriggerTransitionLog();

        $trigger = $this->createTrigger(
            $transitionLogMock,
            $finderMock,
            $stateMachinePersistenceMock,
            $conditionMock,
        );

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setStateMachineName(static::TESTING_STATE_MACHINE);
        $stateMachineProcessTransfer->setProcessName(static::PROCESS_NAME);

        $affectedItems = $trigger->triggerForNewStateMachineItem($stateMachineProcessTransfer, static::ITEM_IDENTIFIER);

        $this->assertSame(1, $affectedItems);
    }

    public function testTriggerEventShouldTriggerSmForGiveItems(): void
    {
        $stateMachinePersistenceMock = $this->createTriggerPersistenceMock();
        $finderMock = $this->createTrigerFinderMock();
        $conditionMock = $this->createTriggerConditionMock();
        $transitionLogMock = $this->createTriggerTransitionLog();

        $trigger = $this->createTrigger(
            $transitionLogMock,
            $finderMock,
            $stateMachinePersistenceMock,
            $conditionMock,
        );

        $stateMachineItemTransfer = $this->createTriggerStateMachineItem();
        $stateMachineItems = [
            $stateMachineItemTransfer,
        ];

        $affectedItems = $trigger->triggerEvent(
            'event',
            $stateMachineItems,
        );

        $this->assertSame(1, $affectedItems);
    }

    public function testTriggerEventRunsCommandByItemsPluginOnceForAllStateMachineItems(): void
    {
        // Arrange
        $stateMachineItemTransfers = [
            $this->createTriggerStateMachineItem(),
            $this->createTriggerStateMachineItem()->setIdentifier(2),
        ];

        $commandByItemsPluginMock = $this->getMockBuilder(CommandByItemsPluginInterface::class)->getMock();

        $commandByItemsPluginMock->expects($this->once())
            ->method('run')
            ->with($stateMachineItemTransfers);

        $trigger = $this->createTriggerWithCommandPlugin($commandByItemsPluginMock);

        // Act
        $affectedItems = $trigger->triggerEvent('event', $stateMachineItemTransfers);

        // Assert
        $this->assertSame(2, $affectedItems);
    }

    public function testTriggerEventRunsCommandPluginPerStateMachineItem(): void
    {
        // Arrange
        $stateMachineItemTransfers = [
            $this->createTriggerStateMachineItem(),
            $this->createTriggerStateMachineItem()->setIdentifier(2),
        ];

        $commandPluginMock = $this->getMockBuilder(CommandPluginInterface::class)->getMock();

        $commandPluginMock->expects($this->exactly(2))->method('run');

        $trigger = $this->createTriggerWithCommandPlugin($commandPluginMock);

        // Act
        $affectedItems = $trigger->triggerEvent('event', $stateMachineItemTransfers);

        // Assert
        $this->assertSame(2, $affectedItems);
    }

    /**
     * @param \Spryker\Zed\StateMachine\Dependency\Plugin\CommandPluginInterface|\Spryker\Zed\StateMachine\Dependency\Plugin\CommandByItemsPluginInterface $commandPlugin
     * @param array<\Spryker\Zed\StateMachine\Business\Process\ProcessInterface>|null $processes
     */
    protected function createTriggerWithCommandPlugin(
        object $commandPlugin,
        ?array $processes = null,
        ?TransitionLogInterface $transitionLogMock = null
    ): Trigger {
        return $this->createTriggerWithCommandPlugins(
            [static::TEST_COMMAND => $commandPlugin],
            $processes,
            $transitionLogMock,
        );
    }

    /**
     * @param array<string, object> $commandPlugins
     * @param array<\Spryker\Zed\StateMachine\Business\Process\ProcessInterface>|null $processes
     */
    protected function createTriggerWithCommandPlugins(
        array $commandPlugins,
        ?array $processes = null,
        ?TransitionLogInterface $transitionLogMock = null
    ): Trigger {
        $processes = $processes ?? $this->createProcesses();
        $finderMock = $this->createFinderMock();
        $finderMock->method('findProcessesForItems')->willReturn($processes);
        $finderMock->method('findProcessByStateMachineProcess')->willReturn($processes[static::PROCESS_NAME]);
        $finderMock->method('filterItemsWithOnEnterEvent')->willReturn([]);

        $persistenceMock = $this->createPersistenceMock();
        $persistenceMock->method('updateStateMachineItemsFromPersistence')->willReturnCallback(
            function (array $stateMachineItemTransfers): array {
                return $stateMachineItemTransfers;
            },
        );

        $targetState = new State();
        $targetState->setName('target state');
        $conditionMock = $this->createConditionMock();
        $conditionMock->method('getTargetStatesFromTransitions')->willReturn($targetState);

        $handlerResolverMock = $this->createHandlerResolverMock();
        $handlerMock = $this->createStateMachineHandlerMock();
        $handlerMock->method('getActiveProcesses')->willReturn([static::PROCESS_NAME]);
        $handlerMock->method('getInitialStateForProcess')->willReturn(static::INITIAL_STATE);
        $handlerMock->method('getCommandPlugins')->willReturn($commandPlugins);
        $handlerResolverMock->method('get')->willReturn($handlerMock);

        return $this->createTrigger(
            $transitionLogMock ?? $this->createTransitionLogMock(),
            $finderMock,
            $persistenceMock,
            $conditionMock,
            null,
            $handlerResolverMock,
        );
    }

    /**
     * A process whose two states resolve the same event to two different commands.
     *
     * @return array<\Spryker\Zed\StateMachine\Business\Process\ProcessInterface>
     */
    protected function createProcessesWithTwoStates(): array
    {
        $process = new Process();

        foreach ([static::INITIAL_STATE => static::TEST_COMMAND, static::OTHER_STATE_NAME => static::OTHER_TEST_COMMAND] as $stateName => $commandName) {
            $event = new Event();
            $event->setName('event');
            $event->setCommand($commandName);

            $sourceState = new State();
            $sourceState->setName($stateName);

            $transition = new Transition();
            $transition->setSourceState($sourceState);
            $event->addTransition($transition);

            $outgoingTransition = new Transition();
            $outgoingTransition->setEvent($event);

            $state = new State();
            $state->setName($stateName);
            $state->addOutgoingTransition($outgoingTransition);

            $process->addState($state);
        }

        return [static::PROCESS_NAME => $process];
    }

    /**
     * Items of one event can sit in states resolving to different commands. Each command must receive its
     * own items only, and none of them may be skipped.
     */
    public function testTriggerEventRunsEachCommandWithItsOwnStateMachineItemsOnly(): void
    {
        // Arrange
        $itemOfCommandByItems = $this->createTriggerStateMachineItem();
        $itemOfCommandPlugin = $this->createTriggerStateMachineItem()->setIdentifier(2)->setStateName(static::OTHER_STATE_NAME);

        $commandByItemsPluginMock = $this->getMockBuilder(CommandByItemsPluginInterface::class)->getMock();
        $commandByItemsPluginMock->expects($this->once())
            ->method('run')
            ->with([$itemOfCommandByItems]);

        $commandPluginMock = $this->getMockBuilder(CommandPluginInterface::class)->getMock();
        $commandPluginMock->expects($this->once())
            ->method('run')
            ->with($itemOfCommandPlugin);

        $trigger = $this->createTriggerWithCommandPlugins([
            static::TEST_COMMAND => $commandByItemsPluginMock,
            static::OTHER_TEST_COMMAND => $commandPluginMock,
        ], $this->createProcessesWithTwoStates());

        // Act
        $trigger->triggerEvent('event', [$itemOfCommandByItems, $itemOfCommandPlugin]);
    }

    public function testTriggerEventThrowsExceptionWhenCommandImplementsNoKnownInterface(): void
    {
        // Arrange
        $trigger = $this->createTriggerWithCommandPlugin(new stdClass());

        // Assert
        $this->expectException(LogicException::class);

        // Act
        $trigger->triggerEvent('event', [$this->createTriggerStateMachineItem()]);
    }

    public function testTriggerEventLogsTheCommandOfACommandByItemsPlugin(): void
    {
        // Arrange
        $stateMachineItemTransfers = [$this->createTriggerStateMachineItem()];
        $commandByItemsPluginMock = $this->getMockBuilder(CommandByItemsPluginInterface::class)->getMock();

        $transitionLogMock = $this->createTransitionLogMock();
        $transitionLogMock->expects($this->once())
            ->method('addCommandByItems')
            ->with($stateMachineItemTransfers, $commandByItemsPluginMock);

        $trigger = $this->createTriggerWithCommandPlugin($commandByItemsPluginMock, null, $transitionLogMock);

        // Act
        $trigger->triggerEvent('event', $stateMachineItemTransfers);
    }

    public function testTriggerConditionsWithoutEventShouldExecuteConditionCheckAndTriggerEvents(): void
    {
        $stateMachinePersistenceMock = $this->createTriggerPersistenceMock();
        $finderMock = $this->createTrigerFinderMock();
        $conditionMock = $this->createTriggerConditionMock();
        $transitionLogMock = $this->createTriggerTransitionLog();

        $conditionMock->expects($this->once())
            ->method('getOnEnterEventsForStatesWithoutTransition')
            ->willReturn($this->createStateMachineItems());

        $trigger = $this->createTrigger(
            $transitionLogMock,
            $finderMock,
            $stateMachinePersistenceMock,
            $conditionMock,
        );

        $affectedItems = $trigger->triggerConditionsWithoutEvent(static::TESTING_STATE_MACHINE);

        $this->assertSame(1, $affectedItems);
    }

    public function testTriggerForTimeoutExpiredItemsShouldExecuteSMOnItemsWithExpiredTimeout(): void
    {
        $stateMachinePersistenceMock = $this->createTriggerPersistenceMock();
        $finderMock = $this->createTrigerFinderMock();
        $conditionMock = $this->createTriggerConditionMock();
        $transitionLogMock = $this->createTriggerTransitionLog();

        $stateMachinePersistenceMock->expects($this->once())
            ->method('getItemsWithExpiredTimeouts')
            ->willReturn([$this->createTriggerStateMachineItem()]);

        $trigger = $this->createTrigger(
            $transitionLogMock,
            $finderMock,
            $stateMachinePersistenceMock,
            $conditionMock,
        );

        $affectedItems = $trigger->triggerForTimeoutExpiredItems(static::TESTING_STATE_MACHINE);

        $this->assertSame(1, $affectedItems);
    }

    public function testTriggerShouldLogTransitionsForTriggerEvent(): void
    {
        $stateMachinePersistenceMock = $this->createTriggerPersistenceMock();
        $finderMock = $this->createTrigerFinderMock();
        $conditionMock = $this->createTriggerConditionMock();

        $transitionLogMock = $this->createTransitionLogMock();
        $transitionLogMock->expects($this->exactly(1))->method('setEvent');

        $trigger = $this->createTrigger(
            $transitionLogMock,
            $finderMock,
            $stateMachinePersistenceMock,
            $conditionMock,
        );

        $stateMachineItems = [
            $this->createTriggerStateMachineItem(),
        ];

        $trigger->triggerEvent(
            'event',
            $stateMachineItems,
        );
    }

    /**
     * @return array<\Spryker\Zed\StateMachine\Business\Process\Process>
     */
    protected function createProcesses(): array
    {
        $processes = [];
        $process = new Process();

        $event = new Event();
        $event->setName('event');
        $event->setCommand(static::TEST_COMMAND);

        $transition = new Transition();
        $state = new State();
        $state->setName('new');
        $transition->setSourceState($state);

        $event->addTransition($transition);

        $outgoingTransitions = new Transition();
        $outgoingTransitions->setEvent($event);

        $state = new State();
        $state->setName('new');
        $state->addOutgoingTransition($outgoingTransitions);

        $process->addState($state);

        $processes[static::PROCESS_NAME] = $process;

        return $processes;
    }

    /**
     * @return array<\Generated\Shared\Transfer\StateMachineItemTransfer>
     */
    protected function createStateMachineItems(): array
    {
        $items = [];

        $items['event'] = [];
        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setProcessName(static::PROCESS_NAME);
        $stateMachineItemTransfer->setIdentifier(1);
        $stateMachineItemTransfer->setStateName('new');
        $items['event'][] = $stateMachineItemTransfer;

        return $items;
    }

    protected function createTrigger(
        ?TransitionLogInterface $transitionLogMock = null,
        ?FinderInterface $finderMock = null,
        ?PersistenceInterface $persistenceMock = null,
        ?ConditionInterface $conditionMock = null,
        ?StateUpdaterInterface $stateUpdaterMock = null,
        ?HandlerResolverInterface $handlerResolverMock = null
    ): Trigger {
        if ($transitionLogMock === null) {
            $transitionLogMock = $this->createTransitionLogMock();
        }

        if ($handlerResolverMock === null) {
            $handlerResolverMock = $this->createHandlerResolverMock();

            $commandMock = $this->createCommandMock();

            $handlerMock = $this->createStateMachineHandlerMock();
            $handlerMock->method('getActiveProcesses')->willReturn([static::PROCESS_NAME]);
            $handlerMock->method('getInitialStateForProcess')->willReturn(static::INITIAL_STATE);
            $handlerMock->method('getCommandPlugins')->willReturn([
                static::TEST_COMMAND => $commandMock,
            ]);
            $handlerResolverMock->method('get')->willReturn($handlerMock);
        }

        if ($finderMock === null) {
            $finderMock = $this->createFinderMock();
        }

        if ($persistenceMock === null) {
            $persistenceMock = $this->createPersistenceMock();
        }

        if ($stateUpdaterMock === null) {
            $stateUpdaterMock = $this->createStateUpdaterMock();
        }

        if ($conditionMock === null) {
            $conditionMock = $this->createConditionMock();
        }

        return new Trigger(
            $transitionLogMock,
            $handlerResolverMock,
            $finderMock,
            $persistenceMock,
            $conditionMock,
            $stateUpdaterMock,
            new ProcessKeyBuilder(),
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected function createTriggerPersistenceMock(): PersistenceInterface
    {
        $stateMachinePersistenceMock = $this->createPersistenceMock();
        $stateMachinePersistenceMock->expects($this->once())
            ->method('updateStateMachineItemsFromPersistence')
            ->willReturnCallback(
                function ($stateMachineItems) {
                    return $stateMachineItems;
                },
            );

        return $stateMachinePersistenceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\StateMachine\Business\StateMachine\FinderInterface
     */
    protected function createTrigerFinderMock(): FinderInterface
    {
        $finderMock = $this->createFinderMock();
        $finderMock->expects($this->once())
            ->method('findProcessesForItems')
            ->willReturn($this->createProcesses());

        $finderMock->expects($this->once())
            ->method('findProcessByStateMachineProcess')
            ->willReturn($this->createProcesses()[static::PROCESS_NAME]);

        $finderMock->expects($this->once())
            ->method('filterItemsWithOnEnterEvent')
            ->willReturn([]);

        return $finderMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\StateMachine\Business\StateMachine\ConditionInterface
     */
    protected function createTriggerConditionMock(): ConditionInterface
    {
        $conditionMock = $this->createConditionMock();
        $targetState = new State();
        $targetState->setName('target state');
        $conditionMock->expects($this->once())
            ->method('getTargetStatesFromTransitions')
            ->willReturn($targetState);

        return $conditionMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\StateMachine\Business\Logger\TransitionLogInterface
     */
    protected function createTriggerTransitionLog(): TransitionLogInterface
    {
        $transitionLogMock = $this->createTransitionLogMock();
        $transitionLogMock->expects($this->once())->method('init');
        $transitionLogMock->expects($this->once())->method('setEvent');
        $transitionLogMock->expects($this->exactly(2))->method('addSourceState');
        $transitionLogMock->expects($this->once())->method('addTargetState');
        $transitionLogMock->expects($this->once())->method('saveAll');

        return $transitionLogMock;
    }

    protected function createTriggerStateMachineItem(): StateMachineItemTransfer
    {
        $stateMachineItemTransfer = new StateMachineItemTransfer();
        $stateMachineItemTransfer->setIdentifier(1);
        $stateMachineItemTransfer->setStateName('new');
        $stateMachineItemTransfer->setIdItemState(1);
        $stateMachineItemTransfer->setEventName('event');
        $stateMachineItemTransfer->setProcessName(static::PROCESS_NAME);

        return $stateMachineItemTransfer;
    }
}
