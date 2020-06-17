<?php

namespace TBoileau\DomainFlow\Tests\Fixtures;

use PHPUnit\Framework\Assert;
use TBoileau\DomainFlow\Handler\HandlerInterface;

/**
 * Class HandlerThatNormalizeInvalidRequest
 * @package TBoileau\DomainFlow\Tests\Fixtures
 */
class HandlerThatNormalizeInvalidRequest implements HandlerInterface
{
    public function valid(array $query): void
    {
        Assert::assertNotEmpty($query["foo"]);
    }

    public function normalize(?array $data = null): object
    {
        return new \stdClass();
    }
}
