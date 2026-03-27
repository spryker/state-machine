<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Business\Resolver;

use Generated\Shared\Transfer\StateMachineProcessTransfer;

interface PathResolverInterface
{
    public function getPathToProcessXmlFile(StateMachineProcessTransfer $stateMachineProcessTransfer): string;

    public function buildPathToXml(StateMachineProcessTransfer $stateMachineProcessTransfer): string;

    public function getXmlFromFileName(string $pathToXml, string $fileName): string;
}
