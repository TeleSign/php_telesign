<?php

namespace telesign\sdk;
use Composer\InstalledVersions;

class Config {
    public static function getVersion()
    {
        return InstalledVersions::getPrettyVersion('telesign/telesign');
    }
}