<?php

namespace TBoileau\DomainFlow\Tests\UnitTests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TBoileau\DomainFlow\Exception\InvalidArgumentException;
use TBoileau\DomainFlow\Exception\InvalidHandlerException;
use TBoileau\DomainFlow\Exception\InvalidUseCaseException;
use TBoileau\DomainFlow\Tests\Fixtures\DummyHandler;
use TBoileau\DomainFlow\Tests\Fixtures\DummyPresenter;
use TBoileau\DomainFlow\Tests\Fixtures\DummyRequest;
use TBoileau\DomainFlow\Tests\Fixtures\DummyUseCase;
use TBoileau\DomainFlow\Tests\Fixtures\DummyUseCaseWithInvoke;
use TBoileau\DomainFlow\Tests\Fixtures\HandlerThatNormalizeInvalidRequest;
use TBoileau\DomainFlow\Tests\Fixtures\UseCaseWithExecuteAndInvokeMethods;
use TBoileau\DomainFlow\Tests\Fixtures\UseCaseWithMultipleArguments;
use TBoileau\DomainFlow\Tests\Fixtures\UseCaseWithoutMethod;
use TBoileau\DomainFlow\Tests\Fixtures\UseCaseWithoutRequest;
use TBoileau\DomainFlow\UseCase\UseCaseFactory;

/**
 * Class UseCaseTest
 * @package TBoileau\DomainFlow\Tests\UnitTests
 * @author Thomas Boileau <t-boileau@email.com>
 * @copyright 2020 Thomas Boileau
 */
class UseCaseTest extends TestCase
{
    public function test use case with request and execute method that return a response()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method("has")->willReturn(true);

        $container->method("get")->willReturnMap([
            [DummyUseCase::class, new DummyUseCase()],
            [DummyHandler::class, new DummyHandler()]
        ]);

        $presenter = new DummyPresenter();

        $useCaseFactory = new UseCaseFactory($container);

        $useCaseFactory
            ->given(DummyUseCase::class)
            ->when(DummyHandler::class)
            ->with("foo", "bar")
            ->then($presenter)
        ;

        $this->assertEquals("bar", $presenter->foo);
    }

    public function test use case with request and __invoke method that return a response()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method("has")->willReturn(true);

        $container->method("get")->willReturnMap([
            [DummyUseCaseWithInvoke::class, new DummyUseCaseWithInvoke()],
            [DummyHandler::class, new DummyHandler()]
        ]);

        $presenter = new DummyPresenter();

        $useCaseFactory = new UseCaseFactory($container);

        $useCaseFactory
            ->given(DummyUseCaseWithInvoke::class)
            ->when(DummyHandler::class)
            ->with("foo", "bar")
            ->then($presenter)
        ;

        $this->assertEquals("bar", $presenter->foo);
    }

    public function test invalid use case()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method("has")->willReturn(false);

        $useCaseFactory = new UseCaseFactory($container);

        $this->expectException(InvalidUseCaseException::class);
        $this->expectExceptionMessage(sprintf("Use case %s is not valid.", DummyUseCase::class));

        $useCaseFactory->given(DummyUseCase::class);
    }

    public function test use case that does implement neither execute or invoke method()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method("has")->willReturn(true);

        $container->method("get")->willReturnMap([
            [UseCaseWithoutMethod::class, new UseCaseWithoutMethod()]
        ]);

        $useCaseFactory = new UseCaseFactory($container);

        $this->expectException(InvalidUseCaseException::class);
        $this->expectExceptionMessage(
            sprintf(
                "Use case %s must have 'execute' or '__invoke' method.",
                UseCaseWithoutMethod::class
            )
        );

        $useCaseFactory->given(UseCaseWithoutMethod::class);
    }

    public function test use case that does implement neither execute and invoke methods()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method("has")->willReturn(true);

        $container->method("get")->willReturnMap([
            [UseCaseWithExecuteAndInvokeMethods::class, new UseCaseWithExecuteAndInvokeMethods()]
        ]);

        $useCaseFactory = new UseCaseFactory($container);

        $this->expectException(InvalidUseCaseException::class);
        $this->expectExceptionMessage(
            sprintf(
                "Use case %s must have 'execute' or '__invoke' method, but not both.",
                UseCaseWithExecuteAndInvokeMethods::class
            )
        );

        $useCaseFactory->given(UseCaseWithExecuteAndInvokeMethods::class);
    }

    public function test use case that does implement execute or invoke method with multiple arguments()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method("has")->willReturn(true);

        $container->method("get")->willReturnMap([
            [UseCaseWithMultipleArguments::class, new UseCaseWithMultipleArguments()]
        ]);

        $useCaseFactory = new UseCaseFactory($container);

        $this->expectException(InvalidUseCaseException::class);
        $this->expectExceptionMessage(
            sprintf(
                "The method %s cannot have more than one parameter.",
                "execute"
            )
        );

        $useCaseFactory->given(UseCaseWithMultipleArguments::class);
    }

    public function test use case with invalid handler()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method("has")->willReturnMap([
            [DummyUseCase::class, true],
            [DummyHandler::class, false]
        ]);

        $container->method("get")->willReturnMap([
            [DummyUseCase::class, new DummyUseCase()]
        ]);

        $useCaseFactory = new UseCaseFactory($container);

        $this->expectException(InvalidHandlerException::class);
        $this->expectExceptionMessage(sprintf("Handler %s is not valid.", DummyHandler::class));

        $useCaseFactory->given(DummyUseCase::class)->when(DummyHandler::class);
    }

    public function test use case by adding existing argument()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method("has")->willReturn(true);

        $container->method("get")->willReturnMap([
            [DummyUseCase::class, new DummyUseCase()],
            [DummyHandler::class, new DummyHandler()]
        ]);

        $useCaseFactory = new UseCaseFactory($container);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf("Argument %s is not valid.", "foo"));

        $useCaseFactory
            ->given(DummyUseCase::class)
            ->when(DummyHandler::class)
            ->with("foo", "bar")
            ->with("foo", "bar")
        ;
    }

    public function test use case with no request but handler defined()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method("has")->willReturn(true);

        $container->method("get")->willReturnMap([
            [UseCaseWithoutRequest::class, new UseCaseWithoutRequest()],
            [DummyHandler::class, new DummyHandler()]
        ]);

        $useCaseFactory = new UseCaseFactory($container);

        $this->expectException(InvalidHandlerException::class);
        $this->expectExceptionMessage(
            sprintf(
                "You cannot define a handler for the use case %s that does not need a request in %s.",
                UseCaseWithoutRequest::class,
                "execute"
            )
        );

        $presenter = new DummyPresenter();

        $useCaseFactory
            ->given(UseCaseWithoutRequest::class)
            ->when(DummyHandler::class)
            ->then($presenter)
        ;
    }

    public function test use case with request that not match with normalized request data()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method("has")->willReturn(true);

        $container->method("get")->willReturnMap([
            [DummyUseCase::class, new DummyUseCase()],
            [HandlerThatNormalizeInvalidRequest::class, new HandlerThatNormalizeInvalidRequest()]
        ]);

        $presenter = new DummyPresenter();

        $useCaseFactory = new UseCaseFactory($container);

        $this->expectException(InvalidUseCaseException::class);
        $this->expectExceptionMessage(
            sprintf(
                "Method %s in %s must be %s, you passed %s.",
                "execute",
                DummyUseCase::class,
                DummyRequest::class,
                \stdClass::class
            )
        );

        $useCaseFactory
            ->given(DummyUseCase::class)
            ->when(HandlerThatNormalizeInvalidRequest::class)
            ->with("foo", "bar")
            ->then($presenter)
        ;
    }

    public function test use case with request but no handler()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method("has")->willReturn(true);

        $container->method("get")->willReturnMap([
            [DummyUseCase::class, new DummyUseCase()]
        ]);

        $presenter = new DummyPresenter();

        $useCaseFactory = new UseCaseFactory($container);

        $this->expectException(InvalidUseCaseException::class);
        $this->expectExceptionMessage(
            sprintf(
                "You cannot inject a request in %s of %s if you have not defined a request handler.",
                "execute",
                DummyUseCase::class
            )
        );

        $useCaseFactory
            ->given(DummyUseCase::class)
            ->with("foo", "bar")
            ->then($presenter)
        ;
    }
}
