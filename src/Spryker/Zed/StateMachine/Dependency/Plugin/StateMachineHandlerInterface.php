<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Dependency\Plugin;

use Generated\Shared\Transfer\StateMachineItemTransfer;

interface StateMachineHandlerInterface
{

    /**
     * List of command plugins for this state machine for all processes.
     *
     * @return \Spryker\Zed\StateMachine\Dependency\Plugin\CommandPluginInterface[]
     */
    public function getCommandPlugins();

    /**
     * List of condition plugins for this state machine for all processes.
     *
     * @return \Spryker\Zed\StateMachine\Dependency\Plugin\ConditionPluginInterface[]
     */
    public function getConditionPlugins();

    /**
     * Name of state machine used by this handler.
     *
     * @return string
     */
    public function getStateMachineName();

    /**
     * List of active processes used for this state machine
     *
     * @return string[]
     */
    public function getActiveProcesses();

    /**
     * Provide initial state name for item when state machine initialized. Using process name.
     *
     * @param string $processName
     *
     * @return string
     */
    public function getInitialStateForProcess($processName);

    /**
     * This method is called when state of item was changed, client can create custom logic for example update it's related table with new stateId and processId.
     * StateMachineItemTransfer:identifier is id of entity from client.
     *
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    public function itemStateUpdated(StateMachineItemTransfer $stateMachineItemTransfer);

    /**
     * This method returns list of StateMachineItemTransfer with identifier, processId and stateId
     *
     * @param int[] $stateIds
     *
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer[]
     */
    public function getStateMachineItemsByStateIds(array $stateIds = []);

}
