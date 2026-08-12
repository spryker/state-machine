<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Business\StateMachine;

use Spryker\Zed\StateMachine\Business\Exception\StateMachineHandlerNotFound;

class HandlerResolver implements HandlerResolverInterface
{
    /**
     * @var array<\Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerInterface>
     */
    protected $handlers = [];

    /**
     * @var array<\Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerResolverPluginInterface>
     */
    protected array $stateMachineHandlerResolverPlugins = [];

    /**
     * @param array<\Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerInterface> $handlers
     * @param array<\Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerResolverPluginInterface> $stateMachineHandlerResolverPlugins
     */
    public function __construct(array $handlers, array $stateMachineHandlerResolverPlugins = [])
    {
        $this->handlers = $handlers;
        $this->stateMachineHandlerResolverPlugins = $stateMachineHandlerResolverPlugins;
    }

    /**
     * @param string $stateMachineName
     *
     * @throws \Spryker\Zed\StateMachine\Business\Exception\StateMachineHandlerNotFound
     *
     * @return \Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerInterface
     */
    public function get($stateMachineName)
    {
        $stateMachineHandler = $this->find($stateMachineName);
        if ($stateMachineHandler !== null) {
            return $stateMachineHandler;
        }

        throw new StateMachineHandlerNotFound(
            sprintf(
                'State machine handler with name "%s" not found',
                $stateMachineName,
            ),
        );
    }

    /**
     * @param string $stateMachineName
     *
     * @return \Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerInterface|null
     */
    public function find($stateMachineName)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->getStateMachineName() === $stateMachineName) {
                return $handler;
            }
        }

        foreach ($this->stateMachineHandlerResolverPlugins as $stateMachineHandlerResolverPlugin) {
            $handler = $stateMachineHandlerResolverPlugin->resolveStateMachineHandler($stateMachineName);
            if ($handler !== null) {
                return $handler;
            }
        }

        return null;
    }
}
