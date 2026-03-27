<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Business\Resolver;

use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Spryker\Zed\StateMachine\Business\Exception\StateMachineException;
use Spryker\Zed\StateMachine\StateMachineConfig;

class PathResolver implements PathResolverInterface
{
    public function __construct(
        protected StateMachineConfig $stateMachineConfig
    ) {
    }

    public function getPathToProcessXmlFile(StateMachineProcessTransfer $stateMachineProcessTransfer): string
    {
        return $this->getXmlFromFileName($this->buildPathToXml($stateMachineProcessTransfer), $stateMachineProcessTransfer->getProcessName());
    }

    public function buildPathToXml(StateMachineProcessTransfer $stateMachineProcessTransfer): string
    {
        $stateMachineProcessTransfer->requireStateMachineName();

        return $this->stateMachineConfig->getPathToStateMachineXmlFiles() . DIRECTORY_SEPARATOR . $stateMachineProcessTransfer->getStateMachineName();
    }

    public function getXmlFromFileName(string $pathToXml, string $fileName): string
    {
        $pathToXml = $pathToXml . DIRECTORY_SEPARATOR . $fileName . '.xml';

        if (!$this->isValidPath($pathToXml)) {
            throw new StateMachineException(
                sprintf(
                    'State machine XML file not found in "%s".',
                    str_replace(APPLICATION_ROOT_DIR, '', $this->stateMachineConfig->getPathToStateMachineXmlFiles()),
                ),
            );
        }

        $xmlContents = file_get_contents($pathToXml);
        if ($xmlContents === false) {
            throw new StateMachineException(
                sprintf(
                    'State machine XML file "%s" could not be read.',
                    $pathToXml,
                ),
            );
        }

        return $pathToXml;
    }

    protected function isValidPath(string $pathToXml): bool
    {
        $realPathToXml = realpath($pathToXml);
        $realPathToStateMachineXmlFiles = realpath($this->stateMachineConfig->getPathToStateMachineXmlFiles());

        return $realPathToXml && strpos($realPathToXml, $realPathToStateMachineXmlFiles . '/') === 0;
    }
}
