<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Dependency\Service;

interface StateMachineToUtilSanitizeXssServiceInterface
{
    /**
     * @param string $text
     * @param list<string> $allowedAttributes
     * @param list<string> $allowedHtmlTags
     *
     * @return string
     */
    public function sanitizeXss(string $text, array $allowedAttributes = [], array $allowedHtmlTags = []): string;
}
