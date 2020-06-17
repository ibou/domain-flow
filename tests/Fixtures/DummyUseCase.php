<?php

namespace TBoileau\DomainFlow\Tests\Fixtures;

/**
 * Class DummyUseCase
 * @package TBoileau\DomainFlow\Tests\Fixtures
 */
class DummyUseCase
{
    public function execute(DummyRequest $request): DummyResponse
    {
        return new DummyResponse($request->foo);
    }
}
