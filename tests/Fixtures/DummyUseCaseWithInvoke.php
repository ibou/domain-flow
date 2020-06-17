<?php

namespace TBoileau\DomainFlow\Tests\Fixtures;

/**
 * Class DummyUseCaseWithInvoke
 * @package TBoileau\DomainFlow\Tests\Fixtures
 */
class DummyUseCaseWithInvoke
{
    public function __invoke(DummyRequest $request): DummyResponse
    {
        return new DummyResponse($request->foo);
    }
}
