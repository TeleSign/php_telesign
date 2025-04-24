<?php

namespace telesign\sdk;

use PHPUnit\Framework\TestCase;
use telesign\sdk\Config;

final class ConfigTest extends TestCase {

  function testGetVersion () {
    $currentVersion = Config::getVersion();
    $this->assertEquals($currentVersion, 'dev-master');
  }
}
