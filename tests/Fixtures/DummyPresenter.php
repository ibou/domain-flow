<?php

namespace TBoileau\DomainFlow\Tests\Fixtures;

use TBoileau\DomainFlow\Presenter\PresenterInterface;

/**
 * Class DummyPresenter
 * @package TBoileau\DomainFlow\Tests\Fixtures
 */
class DummyPresenter implements PresenterInterface
{
    public string $foo;

    /**
     * @param DummyResponse $response
     */
    public function present($response = null): void
    {
        $this->foo = $response->foo;
    }
}
