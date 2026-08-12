<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Business\StateMachine;

use Exception;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Spryker\Zed\StateMachine\Business\Exception\ConditionNotFoundException;
use Spryker\Zed\StateMachine\Business\Logger\TransitionLogInterface;
use Spryker\Zed\StateMachine\Business\Process\ProcessInterface;
use Spryker\Zed\StateMachine\Business\Process\StateInterface;
use Spryker\Zed\StateMachine\Dependency\Plugin\PersistentStateMachineHandlerInterface;
use Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerInterface;

class Condition implements ConditionInterface
{
    /**
     * @var array
     */
    protected $eventCounter = [];

    /**
     * @var array
     */
    protected $processBuffer = [];

    /**
     * @var \Spryker\Zed\StateMachine\Business\Logger\TransitionLogInterface
     */
    protected $transitionLog;

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected $stateMachineHandlerResolver;

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\FinderInterface
     */
    protected $finder;

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected $stateMachinePersistence;

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\StateUpdaterInterface
     */
    protected $stateUpdater;

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\ProcessKeyBuilderInterface
     */
    protected $processKeyBuilder;

    public function __construct(
        TransitionLogInterface $transitionLog,
        HandlerResolverInterface $stateMachineHandlerResolver,
        FinderInterface $finder,
        PersistenceInterface $stateMachinePersistence,
        StateUpdaterInterface $stateUpdate,
        ProcessKeyBuilderInterface $processKeyBuilder
    ) {
        $this->transitionLog = $transitionLog;
        $this->stateMachineHandlerResolver = $stateMachineHandlerResolver;
        $this->finder = $finder;
        $this->stateMachinePersistence = $stateMachinePersistence;
        $this->stateUpdater = $stateUpdate;
        $this->processKeyBuilder = $processKeyBuilder;
    }

    /**
     * @param array<\Spryker\Zed\StateMachine\Business\Process\TransitionInterface> $transitions
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param \Spryker\Zed\StateMachine\Business\Process\StateInterface $sourceState
     * @param \Spryker\Zed\StateMachine\Business\Logger\TransitionLogInterface $transactionLogger
     *
     * @return \Spryker\Zed\StateMachine\Business\Process\StateInterface
     */
    public function getTargetStatesFromTransitions(
        array $transitions,
        StateMachineItemTransfer $stateMachineItemTransfer,
        StateInterface $sourceState,
        TransitionLogInterface $transactionLogger
    ) {
        $possibleTransitions = [];
        foreach ($transitions as $transition) {
            if ($transition->hasCondition()) {
                $isValidCondition = $this->checkCondition(
                    $stateMachineItemTransfer,
                    $transactionLogger,
                    $transition->getCondition(),
                );

                if ($isValidCondition) {
                    array_push($possibleTransitions, $transition);
                }
            } else {
                array_push($possibleTransitions, $transition);
            }
        }

        return $this->findTargetState($sourceState, $possibleTransitions);
    }

    /**
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param \Spryker\Zed\StateMachine\Business\Logger\TransitionLogInterface $transactionLogger
     * @param string $conditionName
     *
     * @throws \Exception
     *
     * @return bool
     */
    protected function checkCondition(
        StateMachineItemTransfer $stateMachineItemTransfer,
        TransitionLogInterface $transactionLogger,
        $conditionName
    ) {
        $conditionPlugin = $this->getConditionPlugin(
            $conditionName,
            $stateMachineItemTransfer->getStateMachineName(),
        );

        try {
            $conditionCheck = $conditionPlugin->check($stateMachineItemTransfer);
        } catch (Exception $e) {
            $transactionLogger->setIsError(true);
            $transactionLogger->setErrorMessage(get_class($conditionPlugin) . ' - ' . $e->getMessage());
            $transactionLogger->saveAll();

            throw $e;
        }

        if ($conditionCheck === true) {
            $transactionLogger->addCondition($stateMachineItemTransfer, $conditionPlugin);

            return true;
        }

        return false;
    }

    /**
     * @param \Spryker\Zed\StateMachine\Business\Process\StateInterface $sourceState
     * @param array<\Spryker\Zed\StateMachine\Business\Process\TransitionInterface> $possibleTransitions
     *
     * @return \Spryker\Zed\StateMachine\Business\Process\StateInterface
     */
    protected function findTargetState(StateInterface $sourceState, array $possibleTransitions)
    {
        $targetState = $sourceState;
        if (count($possibleTransitions) > 0) {
            $selectedTransition = array_shift($possibleTransitions);
            $targetState = $selectedTransition->getTargetState();
        }

        return $targetState;
    }

    /**
     * @param \Generated\Shared\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     *
     * @return array<array<\Generated\Shared\Transfer\StateMachineItemTransfer>>
     */
    public function getOnEnterEventsForStatesWithoutTransition(StateMachineProcessTransfer $stateMachineProcessTransfer)
    {
        $stateMachineName = $stateMachineProcessTransfer->getStateMachineNameOrFail();
        $process = $this->finder->findProcessByStateMachineProcess($stateMachineProcessTransfer);
        $transitions = $process->getAllTransitionsWithoutEvent();

        $stateToTransitionsMap = $this->createStateToTransitionMap($transitions);

        $stateMachineItems = $this->getItemsByStatesAndProcessName($stateMachineProcessTransfer, $stateToTransitionsMap, $process);

        $this->transitionLog->init($stateMachineItems);
        $sourceStates = $this->createStateMap($stateMachineItems);

        $this->persistAffectedStates($stateMachineName, $stateToTransitionsMap, $stateMachineItems);

        $processes = [$this->processKeyBuilder->getProcessKey($process->getName(), $stateMachineProcessTransfer->getVersion()) => $process];

        $this->stateUpdater->updateStateMachineItemState(
            $stateMachineItems,
            $processes,
            $sourceStates,
        );

        $itemsWithOnEnterEvent = $this->finder->filterItemsWithOnEnterEvent(
            $stateMachineItems,
            $processes,
            $sourceStates,
        );

        return $itemsWithOnEnterEvent;
    }

    /**
     * @param \Generated\Shared\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param array<array<\Spryker\Zed\StateMachine\Business\Process\TransitionInterface>> $stateToTransitionsMap
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     *
     * @return array<\Generated\Shared\Transfer\StateMachineItemTransfer>
     */
    protected function getItemsByStatesAndProcessName(
        StateMachineProcessTransfer $stateMachineProcessTransfer,
        array $stateToTransitionsMap,
        ProcessInterface $process
    ) {
        $stateMachineName = $stateMachineProcessTransfer->getStateMachineNameOrFail();
        $stateMachineItemStateIds = $this->stateMachinePersistence->getStateMachineItemIdsByStatesProcessAndStateMachineName(
            $process->getName(),
            $stateMachineName,
            array_keys($stateToTransitionsMap),
        );

        $stateMachineHandler = $this->stateMachineHandlerResolver->get($stateMachineName);

        if ($stateMachineHandler instanceof PersistentStateMachineHandlerInterface) {
            return $stateMachineHandler->getStateMachineItemsForPersistentProcessByStateIds($stateMachineProcessTransfer, $stateMachineItemStateIds);
        }

        $stateMachineItems = $stateMachineHandler->getStateMachineItemsByStateIds($stateMachineItemStateIds);

        return $this->stateMachinePersistence->updateStateMachineItemsFromPersistence($stateMachineItems);
    }

    /**
     * @param string $stateMachineName
     * @param array<array<\Spryker\Zed\StateMachine\Business\Process\TransitionInterface>> $stateToTransitionsMap Keys are state names
     * @param array<\Generated\Shared\Transfer\StateMachineItemTransfer> $stateMachineItems
     *
     * @return void
     */
    protected function persistAffectedStates(
        $stateMachineName,
        array $stateToTransitionsMap,
        array $stateMachineItems
    ) {
        $targetStateMap = [];
        foreach ($stateMachineItems as $i => $stateMachineItemTransfer) {
            $stateName = $stateMachineItemTransfer->getStateName();

            $process = $this->finder->findProcessByStateMachineProcess(
                (new StateMachineProcessTransfer())
                    ->setStateMachineName($stateMachineName)
                    ->setProcessName($stateMachineItemTransfer->getProcessName())
                    ->setVersion($stateMachineItemTransfer->getVersion()),
            );

            $sourceState = $process->getStateFromAllProcesses($stateName);

            $this->transitionLog->addSourceState($stateMachineItemTransfer, $sourceState->getName());

            $transitions = $stateToTransitionsMap[$stateMachineItemTransfer->getStateName()];

            $targetState = $sourceState;
            if (count($transitions) > 0) {
                $targetState = $this->getTargetStatesFromTransitions(
                    $transitions,
                    $stateMachineItemTransfer,
                    $sourceState,
                    $this->transitionLog,
                );
            }

            $this->transitionLog->addTargetState($stateMachineItemTransfer, $targetState->getName());

            $targetStateMap[$i] = $targetState->getName();
        }

        foreach ($stateMachineItems as $i => $stateMachineItemTransfer) {
            $this->stateMachinePersistence->saveStateMachineItem($stateMachineItems[$i], $targetStateMap[$i]);
        }
    }

    /**
     * @param array<\Spryker\Zed\StateMachine\Business\Process\TransitionInterface> $transitions
     *
     * @return array<array<\Spryker\Zed\StateMachine\Business\Process\TransitionInterface>>
     */
    protected function createStateToTransitionMap(array $transitions)
    {
        $stateToTransitionsMap = [];
        foreach ($transitions as $transition) {
            $sourceStateName = $transition->getSourceState()->getName();
            if (array_key_exists($sourceStateName, $stateToTransitionsMap) === false) {
                $stateToTransitionsMap[$sourceStateName] = [];
            }
            $stateToTransitionsMap[$sourceStateName][] = $transition;
        }

        return $stateToTransitionsMap;
    }

    /**
     * @param string $conditionString
     * @param string $stateMachineName
     *
     * @return \Spryker\Zed\StateMachine\Dependency\Plugin\ConditionPluginInterface
     */
    protected function getConditionPlugin($conditionString, $stateMachineName)
    {
        $stateMachineHandler = $this->stateMachineHandlerResolver->get($stateMachineName);

        $this->assertConditionIsSet($conditionString, $stateMachineHandler);

        return $stateMachineHandler->getConditionPlugins()[$conditionString];
    }

    /**
     * @param string $conditionString
     * @param \Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerInterface $stateMachineHandler
     *
     * @throws \Spryker\Zed\StateMachine\Business\Exception\ConditionNotFoundException
     *
     * @return void
     */
    protected function assertConditionIsSet($conditionString, StateMachineHandlerInterface $stateMachineHandler)
    {
        if (!isset($stateMachineHandler->getConditionPlugins()[$conditionString])) {
            throw new ConditionNotFoundException(
                sprintf(
                    'Condition plugin "%s" not registered in "%s" class. Please add it to getConditionPlugins() method.',
                    $conditionString,
                    get_class($this->stateMachineHandlerResolver),
                ),
            );
        }
    }

    /**
     * @param array<\Generated\Shared\Transfer\StateMachineItemTransfer> $stateMachineItemTransfers
     *
     * @return array<string>
     */
    protected function createStateMap(array $stateMachineItemTransfers)
    {
        $sourceStates = [];
        foreach ($stateMachineItemTransfers as $stateMachineItemTransfer) {
            $sourceStates[$stateMachineItemTransfer->getIdentifier()] = $stateMachineItemTransfer->getStateName();
        }

        return $sourceStates;
    }
}
