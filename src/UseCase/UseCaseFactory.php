<?php

namespace TBoileau\DomainFlow\UseCase;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use TBoileau\DomainFlow\Exception\InvalidArgumentException;
use TBoileau\DomainFlow\Exception\InvalidHandlerException;
use TBoileau\DomainFlow\Exception\InvalidUseCaseException;
use TBoileau\DomainFlow\Handler\HandlerInterface;
use TBoileau\DomainFlow\Presenter\PresenterInterface;

/**
 * Class UseCaseFactory
 * @package TBoileau\DomainFlow\UseCase
 */
class UseCaseFactory implements UseCaseFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var object
     */
    private object $useCase;

    /**
     * @var HandlerInterface|null
     */
    private ?HandlerInterface $handler = null;

    /**
     * @var array
     */
    private array $query = [];

    /**
     * @var ReflectionMethod
     */
    private ReflectionMethod $executeMethod;

    /**
     * UseCaseFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $useCase
     * @return $this|UseCaseFactoryInterface
     * @throws ReflectionException
     */
    public function given(string $useCase): UseCaseFactoryInterface
    {
        if (!$this->container->has($useCase)) {
            throw new InvalidUseCaseException(sprintf("Use case %s is not valid.", $useCase));
        }

        $reflectionClass = new ReflectionClass($useCase);

        if (!$reflectionClass->hasMethod("execute") && !$reflectionClass->hasMethod("__invoke")) {
            throw new InvalidUseCaseException(
                sprintf("Use case %s must have 'execute' or '__invoke' method.", $useCase)
            );
        }

        if ($reflectionClass->hasMethod("execute") && $reflectionClass->hasMethod("__invoke")) {
            throw new InvalidUseCaseException(
                sprintf("Use case %s must have 'execute' or '__invoke' method, but not both.", $useCase)
            );
        }

        $this->executeMethod = $reflectionClass->hasMethod("execute")
            ? $reflectionClass->getMethod("execute")
            : $reflectionClass->getMethod("__invoke")
        ;

        if (count($this->executeMethod->getParameters()) > 1) {
            throw new InvalidUseCaseException(
                sprintf("The method %s cannot have more than one parameter.", $this->executeMethod->getName())
            );
        }

        $this->useCase = $this->container->get($useCase);

        return $this;
    }

    /**
     * @param string $handler
     * @return $this|UseCaseFactoryInterface
     */
    public function when(string $handler): UseCaseFactoryInterface
    {
        if (!$this->container->has($handler)) {
            throw new InvalidHandlerException(sprintf("Handler %s is not valid.", $handler));
        }

        $this->handler = $this->container->get($handler);

        return $this;
    }

    /**
     * @param string $key
     * @param $value
     * @return UseCaseFactoryInterface
     */
    public function with(string $key, $value): UseCaseFactoryInterface
    {
        if (isset($this->query[$key])) {
            throw new InvalidArgumentException(sprintf("Argument %s is not valid.", $key));
        }

        $this->query[$key] = $value;

        return $this;
    }

    /**
     * @param PresenterInterface $presenter
     */
    public function then(PresenterInterface $presenter): void
    {
        if (count($this->executeMethod->getParameters()) === 0 && $this->handler !== null) {
            $message = "You cannot define a handler for the use case %s that does not need a request in %s.";
            throw new InvalidHandlerException(
                sprintf($message, get_class($this->useCase), $this->executeMethod->getName())
            );
        }

        if (count($this->executeMethod->getParameters()) > 0 && $this->handler === null) {
            $message = "You cannot inject a request in %s of %s if you have not defined a request handler.";
            throw new InvalidUseCaseException(
                sprintf($message, $this->executeMethod->getName(), get_class($this->useCase))
            );
        }

        $args = [];

        if (count($this->executeMethod->getParameters()) === 1) {
            $this->handler->valid($this->query);

            $request = $this->handler->normalize($this->query);

            if (!$this->executeMethod->getParameters()[0]->getClass()->isInstance($request)) {
                $message = "Method %s in %s must be %s, you passed %s.";
                $args = [
                    $message,
                    $this->executeMethod->getName(),
                    get_class($this->useCase),
                    $this->executeMethod->getParameters()[0]->getClass()->getName(),
                    get_class($request)
                ];
                throw new InvalidUseCaseException(sprintf($message, ...$args));
            }

            $args[] = $request;
        }

        $response = $this->executeMethod->invokeArgs($this->useCase, $args);

        $presenter->present($response);
    }
}
