<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Persistence;

use Generated\Shared\Transfer\StateMachineProcessCriteriaTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;

interface StateMachineRepositoryInterface
{
    public function findStateMachineProcess(StateMachineProcessCriteriaTransfer $stateMachineProcessCriteriaTransfer): ?StateMachineProcessTransfer;
}
