<?php

namespace Novatorius\Siren\ManualAttribution\Strategies;

use PHPNomad\Template\Interfaces\CanResolvePaths;
use PHPNomad\Utils\Helpers\Str;

class PathResolver implements CanResolvePaths
{
    public function getPath(string $assetName = ''): string
    {
        return Str::append(SIREN_MANUAL_AFFILIATE_PAYOUTS_PATH, '/') . Str::trimLeading($assetName, '/');
    }
}