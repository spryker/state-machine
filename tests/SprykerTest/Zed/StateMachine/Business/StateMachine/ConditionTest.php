<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\StateMachine\Business\StateMachine;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Spryker\Zed\StateMachine\Business\Process\State;
use Spryker\Zed\StateMachine\Business\Process\Transition;
use Spryker\Zed\StateMachine\Business\StateMachine\Condition;
use Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface;
use SprykerTest\Zed\StateMachine\Mocks\StateMachineMocks;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group StateMachine
 * @group Business
 * @group StateMachine
 * @group ConditionTest
 * Add your own group annotations below this line
 */
class ConditionTest extends StateMachineMocks
{
    /**
     * @return void
     */
    public function testCheckConditionForTransitionShouldReturnTargetStateOfGivenTransition(): void
    {
        $stateMachineHandlerResolverMock = $this->createStateMachineResolverMock(true);

        $condition = new Condition(
            $this->createTransitionLogMock(),
            $stateMachineHandlerResolverMock,
            $this->createFinderMock(),
            $this->createPersistenceMock(),
            $this->createStateUpdaterMock(),
        );

        $transitions = [];
        $sourceState = new State();
        $sourceState->setName('source state');

        $targetState = new State();
        $targetState->setName('target state');

        $transition = new Transition();
        $transition->setCondition('condition');
        $transition->setSourceState($sourceState);
        $transition->setTargetState($targetState);
        $transitions[] = $transition;

        $processedTargetState = $condition->getTargetStatesFromTransitions(
            $transitions,
            new StateMachineItemTransfer(),
            new State(),
            $this->createTransitionLogMock(),
        );

        $this->assertSame($targetState->getName(), $processedTargetState->getName());
    }

    /**
     * @return void
     */
    public function testCheckConditionForTransitionWhenConditionReturnsFalseShouldReturnSourceState(): void
    {
        $stateMachineHandlerResolverMock = $this->createStateMachineResolverMock(false);

        $condition = new Condition(
            $this->createTransitionLogMock(),
            $stateMachineHandlerResolverMock,
            $this->createFinderMock(),
            $this->createPersistenceMock(),
            $this->createStateUpdaterMock(),
        );

        $transitions = [];
        $sourceState = new State();
        $sourceState->setName('source state');

        $targetState = new State();
        $targetState->setName('target state');

        $transition = new Transition();
        $transition->setCondition('condition');
        $transition->setSourceState($sourceState);
        $transition->setTargetState($targetState);
        $transitions[] = $transition;

        $sourceState = new State();
        $sourceState->setName('initial source');

        $processedTargetState = $condition->getTargetStatesFromTransitions(
            $transitions,
            new StateMachineItemTransfer(),
            $sourceState,
            $this->createTransitionLogMock(),
        );

        $this->assertSame($sourceState->getName(), $processedTargetState->getName());
    }

    /**
     * @param bool $conditionCheckResult
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected function createStateMachineResolverMock(bool $conditionCheckResult): HandlerResolverInterface
    {
        $conditionPluginMock = $this->createConditionPluginMock();
        $conditionPluginMock->expects($this->once())
            ->method('check')
            ->willReturn($conditionCheckResult);

        $stateMachineHandler = $this->createStateMachineHandlerMock();
        $stateMachineHandler->expects($this->exactly(2))
            ->method('getConditionPlugins')
            ->willReturn([
                    'condition' => $conditionPluginMock,
                ]);

        $stateMachineHandlerResolverMock = $this->createHandlerResolverMock();
        $stateMachineHandlerResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($stateMachineHandler);

        return $stateMachineHandlerResolverMock;
    }
}
