<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Persistence;

use Generated\Shared\Transfer\StateMachineProcessCriteriaTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Orm\Zed\StateMachine\Persistence\SpyStateMachineProcessQuery;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\StateMachine\Persistence\StateMachinePersistenceFactory getFactory()
 */
class StateMachineRepository extends AbstractRepository implements StateMachineRepositoryInterface
{
    public function findStateMachineProcess(StateMachineProcessCriteriaTransfer $stateMachineProcessCriteriaTransfer): ?StateMachineProcessTransfer
    {
        $stateMachineProcessQuery = $this->getFactory()->createStateMachineProcessQuery();
        $stateMachineProcessQuery = $this->applyStateMachineProcessFilters($stateMachineProcessQuery, $stateMachineProcessCriteriaTransfer);

        $stateMachineProcessEntity = $stateMachineProcessQuery->findOne();

        if (!$stateMachineProcessEntity) {
            return null;
        }

        return $this->getFactory()
            ->createStateMachineMapper()
            ->mapStateMachineProcessEntityToStateMachineProcessTransfer(
                $stateMachineProcessEntity,
                new StateMachineProcessTransfer(),
            );
    }

    protected function applyStateMachineProcessFilters(
        SpyStateMachineProcessQuery $stateMachineProcessQuery,
        StateMachineProcessCriteriaTransfer $stateMachineProcessCriteriaTransfer
    ): SpyStateMachineProcessQuery {
        if ($stateMachineProcessCriteriaTransfer->getIdStateMachineProcess() !== null) {
            $stateMachineProcessQuery->filterByIdStateMachineProcess($stateMachineProcessCriteriaTransfer->getIdStateMachineProcess());
        }

        return $stateMachineProcessQuery;
    }
}
