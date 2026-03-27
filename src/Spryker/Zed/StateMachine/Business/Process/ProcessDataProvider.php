<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Business\Process;

use Generated\Shared\Transfer\ProcessCriteriaTransfer;
use Generated\Shared\Transfer\ProcessDataTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Spryker\Zed\StateMachine\Business\Resolver\PathResolverInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface;

class ProcessDataProvider implements ProcessDataProviderInterface
{
    public function __construct(
        protected PathResolverInterface $pathResolver,
        protected HandlerResolverInterface $handlerResolver,
    ) {
    }

    public function getProcessData(ProcessCriteriaTransfer $processCriteriaTransfer): ProcessDataTransfer
    {
        $stateMachineProcessTransfer = (new StateMachineProcessTransfer())
            ->setProcessName($processCriteriaTransfer->getProcessName())
            ->setStateMachineName($processCriteriaTransfer->getStateMachineName());

        $stateMachineHandler = $this->handlerResolver->get((string)$processCriteriaTransfer->getStateMachineName());
        $processFilePath = $this->pathResolver->getPathToProcessXmlFile($stateMachineProcessTransfer);
        $commands = array_map(fn ($v) => '', array_flip(array_keys($stateMachineHandler->getCommandPlugins())));
        $conditions = array_map(fn ($v) => '', array_flip(array_keys($stateMachineHandler->getConditionPlugins())));

        return (new ProcessDataTransfer())
            ->setProcessFilePath($processFilePath)
            ->setCommands($commands)
            ->setConditions($conditions);
    }
}
