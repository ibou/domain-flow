<?php

namespace TBoileau\DomainFlow\Tests\Fixtures;

/**
 * Class DummyResponse
 * @package TBoileau\DomainFlow\Tests\Fixtures
 */
class DummyResponse
{
    public string $foo;

    /**
     * DummyResponse constructor.
     * @param string $foo
     */
    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }
}
