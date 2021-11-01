<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Communication\Form;

use Spryker\Service\UtilText\Model\Url\Url;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @method \Spryker\Zed\StateMachine\StateMachineConfig getConfig()
 * @method \Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface getFacade()
 * @method \Spryker\Zed\StateMachine\Communication\StateMachineCommunicationFactory getFactory()
 * @method \Spryker\Zed\StateMachine\Persistence\StateMachineRepositoryInterface getRepository()
 */
class EventItemTriggerForm extends AbstractType
{
    /**
     * @var string
     */
    public const OPTION_EVENT = 'OPTION_EVENT';

    /**
     * @var string
     */
    public const OPTION_SUBMIT_BUTTON_CLASS = 'OPTION_SUBMIT_BUTTON_CLASS';

    /**
     * @var string
     */
    public const OPTION_ACTION_QUERY_PARAMETERS = 'OPTION_ACTION_QUERY_PARAMETERS';

    /**
     * @var string
     */
    protected const BUTTON_SUBMIT = 'submit';

    /**
     * @var string
     */
    protected const ACTION_ROUTE = '/state-machine/trigger/submit-trigger-event-for-new-item';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addSubmitButton($builder, $options)
            ->setAction($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addSubmitButton(FormBuilderInterface $builder, array $options)
    {
        $fieldOptions = [
            'label' => $options[static::OPTION_EVENT],
        ];

        if ($options[static::OPTION_SUBMIT_BUTTON_CLASS]) {
            $fieldOptions['attr'] = [
                'class' => $options[static::OPTION_SUBMIT_BUTTON_CLASS],
            ];
        }

        $builder->add(static::BUTTON_SUBMIT, SubmitType::class, $fieldOptions);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function setAction(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction(
            $this->createAction($options),
        );

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return string
     */
    protected function createAction(array $options): string
    {
        return Url::generate(static::ACTION_ROUTE, $options[static::OPTION_ACTION_QUERY_PARAMETERS]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            static::OPTION_EVENT => null,
            static::OPTION_SUBMIT_BUTTON_CLASS => null,
            static::OPTION_ACTION_QUERY_PARAMETERS => [],
        ]);
    }
}
