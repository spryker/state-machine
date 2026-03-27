<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Business\Process;

use Generated\Shared\Transfer\ProcessCriteriaTransfer;
use Generated\Shared\Transfer\ProcessDataTransfer;

interface ProcessDataProviderInterface
{
    /**
     * Returns process XML file path, commands, and conditions for the given process criteria.
     */
    public function getProcessData(ProcessCriteriaTransfer $processCriteriaTransfer): ProcessDataTransfer;
}
