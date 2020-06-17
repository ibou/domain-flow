<?php

namespace TBoileau\DomainFlow\Tests\Fixtures;

/**
 * Class DummyRequest
 * @package TBoileau\DomainFlow\Tests\Fixtures
 */
class DummyRequest
{
    public string $foo;

    /**
     * DummyRequest constructor.
     * @param string $foo
     */
    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }
}
