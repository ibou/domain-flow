<?php

namespace TBoileau\DomainFlow\Presenter;

/**
 * Interface PresenterInterface
 * @package TBoileau\DomainFlow\Presenter
 */
interface PresenterInterface
{
    /**
     * @param null $response
     */
    public function present($response = null): void;
}
