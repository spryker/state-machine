<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Dependency\Plugin;

/**
 * Command which is executed once for all state machine items an event was triggered for.
 */
interface CommandByItemsPluginInterface
{
    /**
     * Specification:
     * - Called when event have concrete command assigned.
     * - Called once with all state machine items the event was triggered for that resolve to this command.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\StateMachineItemTransfer> $stateMachineItemTransfers
     *
     * @return void
     */
    public function run(array $stateMachineItemTransfers);
}
