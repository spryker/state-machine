<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Business\StateMachine;

class ProcessKeyBuilder implements ProcessKeyBuilderInterface
{
    /**
     * @var string
     */
    protected const VERSION_SEPARATOR = '-v';

    public function getProcessKey(string $processName, ?int $version): string
    {
        if ($version === null) {
            return $processName;
        }

        return $processName . static::VERSION_SEPARATOR . $version;
    }
}
