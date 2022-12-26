<?php

namespace Tests\Helpers;

require_once('helpers/GeoLocator.php');

use Helpers\GeoLocator as GeoLocator;
use PHPUnit\Framework\TestCase;

final class GetRegionTest extends TestCase
{
    public function testGetIpRegion(): void
    {
        $this->assertEquals(
            "Bridgend, Wales - (United Kingdom)",
            GeoLocator::getLocation("188.223.227.125")
        );
        $this->assertEquals(
            "Lupfig, Aargau - (Switzerland)",
            GeoLocator::getLocation("194.191.232.168")
        );
        $this->assertEquals(
            "Amsterdam, North Holland - (Netherlands)",
            GeoLocator::getLocation("77.162.109.160")
        );
    }
}
