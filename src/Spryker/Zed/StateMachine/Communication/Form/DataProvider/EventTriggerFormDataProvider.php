<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Communication\Form\DataProvider;

use Spryker\Zed\StateMachine\Communication\Form\EventTriggerForm;

class EventTriggerFormDataProvider
{
    /**
     * @var string
     */
    protected const SUBMIT_BUTTON_CLASS = 'btn btn-primary btn-sm trigger-event';

    /**
     * @var string
     */
    protected const URL_PARAM_IDENTIFIER = 'identifier';

    /**
     * @var string
     */
    protected const URL_PARAM_ID_STATE = 'id-state';

    /**
     * @var string
     */
    protected const URL_PARAM_REDIRECT = 'redirect';

    /**
     * @var string
     */
    protected const URL_PARAM_EVENT = 'event';

    /**
     * @param int $identifier
     * @param string $redirect
     * @param int $idState
     * @param string $event
     *
     * @return array<string, mixed>
     */
    public function getOptions(
        int $identifier,
        string $redirect,
        int $idState,
        string $event
    ): array {
        return [
            EventTriggerForm::OPTION_EVENT => $event,
            EventTriggerForm::OPTION_ACTION_QUERY_PARAMETERS => [
                static::URL_PARAM_IDENTIFIER => $identifier,
                static::URL_PARAM_REDIRECT => $redirect,
                static::URL_PARAM_ID_STATE => $idState,
                static::URL_PARAM_EVENT => $event,
            ],
            EventTriggerForm::OPTION_SUBMIT_BUTTON_CLASS => static::SUBMIT_BUTTON_CLASS,
        ];
    }
}
