<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Dependency\Plugin;

use Generated\Shared\Transfer\StateMachineProcessTransfer;

/**
 * Extension of the base state machine handler, VERSIONED state machines.
 *
 * The engine treats a handler that also implements this interface as version-aware:
 * it reads the initial state and the definition XML for the bound version (never a file), and scans the
 * versions that carry condition transitions (including inactive-but-live ones) for the scheduled check.
 */
interface PersistentStateMachineHandlerInterface extends StateMachineHandlerInterface
{
    /**
     * Specification:
     * - Provides the initial state name for the given process at its version.
     * - Reads the initial state from the persisted process-definition row.
     * - The null version resolves to the process active version.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     *
     * @return string
     */
    public function getInitialStateForPersistentProcess(StateMachineProcessTransfer $stateMachineProcessTransfer): string;

    /**
     * Specification:
     * - Returns the state machine definition XML for the given process at its version.
     * - Reads from the persisted process definition instead of a file.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     *
     * @return string
     */
    public function getDefinitionXmlForPersistentProcess(StateMachineProcessTransfer $stateMachineProcessTransfer): string;

    /**
     * Specification:
     * - Returns process transfers for the versions that declare condition
     *   transitions (has_condition_transitions = true) and still carry non-finished instances.
     * - Spans ACTIVE and INACTIVE versions: an inactive version starts no new instances but its in-flight
     *   instances must still be advanced by the scheduled condition check.
     * - Each transfer carries its version so the engine builds that version's exact graph.
     *
     * @api
     *
     * @return array<\Generated\Shared\Transfer\StateMachineProcessTransfer>
     */
    public function getProcessesForConditionCheck(): array;

    /**
     * Specification:
     * - Returns the running instances sitting in the given item states, scoped to the process VERSION
     *   carried on the process transfer.
     * - Each returned item carries its version so the engine resolves it against the correct graph.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param array<int> $stateIds
     *
     * @return array<\Generated\Shared\Transfer\StateMachineItemTransfer>
     */
    public function getStateMachineItemsForPersistentProcessByStateIds(
        StateMachineProcessTransfer $stateMachineProcessTransfer,
        array $stateIds = []
    ): array;

    /**
     * Specification:
     * - Sets the process VERSION on each expired-timeout item, resolved from its running instance
     *   (identifier -> instance -> definition -> version).
     * - Expired-timeout items are read from a table that has no version column, so without this the engine
     *   cannot resolve the correct version's graph for a versioned process.
     * - Items whose running instance can no longer be resolved are dropped.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\StateMachineItemTransfer> $stateMachineItemTransfers
     *
     * @return array<\Generated\Shared\Transfer\StateMachineItemTransfer>
     */
    public function expandTimeoutItemsWithVersion(array $stateMachineItemTransfers): array;
}
