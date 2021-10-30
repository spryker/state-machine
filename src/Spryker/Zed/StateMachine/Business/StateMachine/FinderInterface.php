<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Business\StateMachine;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;

interface FinderInterface
{
    /**
     * @param string $stateMachineName
     *
     * @return bool
     */
    public function hasHandler($stateMachineName);

    /**
     * @param string $stateMachineName
     *
     * @return array<\Generated\Shared\Transfer\StateMachineProcessTransfer>
     */
    public function getProcesses($stateMachineName);

    /**
     * @param array<\Generated\Shared\Transfer\StateMachineItemTransfer> $stateMachineItems
     *
     * @return array<array<string>>
     */
    public function getManualEventsForStateMachineItems(array $stateMachineItems);

    /**
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return array<string>
     */
    public function getManualEventsForStateMachineItem(StateMachineItemTransfer $stateMachineItemTransfer);

    /**
     * @param \Generated\Shared\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $flag
     * @param string $sort
     *
     * @return array<\Generated\Shared\Transfer\StateMachineItemTransfer>
     */
    public function getItemsWithFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, $flag, string $sort = 'ASC');

    /**
     * @param \Generated\Shared\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param string $flag
     * @param string $sort
     *
     * @return array<\Generated\Shared\Transfer\StateMachineItemTransfer>
     */
    public function getItemsWithoutFlag(StateMachineProcessTransfer $stateMachineProcessTransfer, $flag, string $sort = 'ASC');

    /**
     * @param array<\Generated\Shared\Transfer\StateMachineItemTransfer> $stateMachineItems
     * @param array<\Spryker\Zed\StateMachine\Business\Process\ProcessInterface> $processes
     * @param array<string> $sourceStates
     *
     * @return array<array<\Generated\Shared\Transfer\StateMachineItemTransfer>>
     */
    public function filterItemsWithOnEnterEvent(
        array $stateMachineItems,
        array $processes,
        array $sourceStates = []
    );

    /**
     * @param string $stateMachineName
     * @param string $processName
     *
     * @return \Spryker\Zed\StateMachine\Business\Process\ProcessInterface
     */
    public function findProcessByStateMachineAndProcessName($stateMachineName, $processName);

    /**
     * @param array<\Generated\Shared\Transfer\StateMachineItemTransfer> $stateMachineItems
     *
     * @return array<\Spryker\Zed\StateMachine\Business\Process\ProcessInterface>
     */
    public function findProcessesForItems(array $stateMachineItems);

    /**
     * @param \Generated\Shared\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     *
     * @return array<string>
     */
    public function getProcessStates(StateMachineProcessTransfer $stateMachineProcessTransfer): array;
}
