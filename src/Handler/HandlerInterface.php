<?php

namespace TBoileau\DomainFlow\Handler;

/**
 * Interface HandlerInterface
 * @package TBoileau\DomainFlow\Handler
 */
interface HandlerInterface
{
    /**
     * @param array $query
     */
    public function valid(array $query): void;

    /**
     * @param array|null $data
     * @return object
     */
    public function normalize(?array $data = null): object;
}
