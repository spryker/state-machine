<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Persistence;

use Orm\Zed\StateMachine\Persistence\Base\SpyStateMachineEventTimeoutQuery;
use Orm\Zed\StateMachine\Persistence\Base\SpyStateMachineItemStateHistoryQuery;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateQuery;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineProcessQuery;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineTransitionLogQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class StateMachinePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineTransitionLogQuery
     */
    public function createStateMachineTransitionLogQuery()
    {
        return SpyStateMachineTransitionLogQuery::create();
    }

    /**
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineProcessQuery
     */
    public function createStateMachineProcessQuery()
    {
        return SpyStateMachineProcessQuery::create();
    }

    /**
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateQuery
     */
    public function createStateMachineItemStateQuery()
    {
        return SpyStateMachineItemStateQuery::create();
    }

    /**
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineEventTimeoutQuery
     */
    public function createStateMachineEventTimeoutQuery()
    {
        return SpyStateMachineEventTimeoutQuery::create();
    }

    /**
     * @return \Orm\Zed\StateMachine\Persistence\SpyStateMachineItemStateHistoryQuery
     */
    public function createStateMachineItemStateHistoryQuery()
    {
        return SpyStateMachineItemStateHistoryQuery::create();
    }

}
