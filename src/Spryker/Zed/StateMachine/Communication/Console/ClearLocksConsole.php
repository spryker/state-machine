<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface getFacade()
 * @method \Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\StateMachine\Communication\StateMachineCommunicationFactory getFactory()
 * @method \Spryker\Zed\StateMachine\Persistence\StateMachineRepositoryInterface getRepository()
 */
class ClearLocksConsole extends Console
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'state-machine:clear-locks';

    /**
     * @var string
     */
    public const COMMAND_DESCRIPTION = 'Clear expired locks from lock table';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(static::COMMAND_NAME);
        $this->setDescription(static::COMMAND_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->getFacade()->clearLocks();

        return static::CODE_SUCCESS;
    }
}
