<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Dependency\Plugin;

interface StateMachineHandlerResolverPluginInterface
{
    /**
     * Specification:
     * - Builds and returns a state machine handler for the given state machine name at runtime.
     * - Used for state machines whose handler is not statically registered (for example DB-authored,
     *   configurable state machines): one handler instance is produced per resolved state machine.
     * - Returns null when this plugin does not handle the given state machine name.
     *
     * @api
     *
     * @param string $stateMachineName
     *
     * @return \Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerInterface|null
     */
    public function resolveStateMachineHandler(string $stateMachineName): ?StateMachineHandlerInterface;
}
