<?php

namespace TBoileau\DomainFlow\UseCase;

use ReflectionException;
use TBoileau\DomainFlow\Exception\InvalidArgumentException;
use TBoileau\DomainFlow\Exception\InvalidHandlerException;
use TBoileau\DomainFlow\Exception\InvalidUseCaseException;
use TBoileau\DomainFlow\Presenter\PresenterInterface;

/**
 * Class UseCaseFactoryInterface
 * @package TBoileau\DomainFlow\UseCase
 */
interface UseCaseFactoryInterface
{
    /**
     * @param string $useCase
     * @return UseCaseFactoryInterface
     * @throws InvalidUseCaseException
     * @throws ReflectionException
     */
    public function given(string $useCase): UseCaseFactoryInterface;

    /**
     * @param string $handler
     * @return UseCaseFactoryInterface
     * @throws InvalidHandlerException
     */
    public function when(string $handler): UseCaseFactoryInterface;

    /**
     * @param string $key
     * @param $value
     * @return UseCaseFactoryInterface
     * @throws InvalidArgumentException
     */
    public function with(string $key, $value): UseCaseFactoryInterface;

    /**
     * @param PresenterInterface $presenter
     * @throws InvalidHandlerException
     * @throws InvalidUseCaseException
     */
    public function then(PresenterInterface $presenter): void;
}
