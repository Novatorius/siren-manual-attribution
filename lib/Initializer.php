<?php

namespace Novatorius\Siren\ManualAttribution;

use PHPNomad\Loader\Interfaces\Loadable;
use Siren\Extensions\Core\Interfaces\Extension;

final class Initializer implements Extension, Loadable
{
    protected bool $isActive = false;

    public function getName(): string
    {
        return 'Manual Collaborator Attribution';
    }

    public function getDescription(): string
    {
        return 'Allows manual attribution of collaborators to transactions.';
    }

    public function canActivate(): bool
    {
        return true;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getSupports(): array
    {
        return [];
    }

    public function load(): void
    {
        $this->isActive = true;
    }
}