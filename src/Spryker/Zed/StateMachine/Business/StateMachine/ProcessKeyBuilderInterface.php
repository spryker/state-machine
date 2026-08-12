<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Business\StateMachine;

interface ProcessKeyBuilderInterface
{
    /**
     * Builds the in-memory key for the per-batch `$processes` map. Version is null for file-based state
     * machines (key = plain process name, unchanged), and appended for DB-authored versioned processes so
     * different versions in one batch resolve to their own parsed graph. Ephemeral key, never persisted.
     */
    public function getProcessKey(string $processName, ?int $version): string;
}
