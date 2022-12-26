<?php
/*
 * Helper class to get geolocation from IP address
 */

namespace Helpers;

class GeoLocator {
    /**
     * @TODO Explore pro solutions
     * GeoIP_ https://github.com/maxmind/GeoIP2-php (DB de pago)
     * Abstract API_ https://www.abstractapi.com/api/ip-geolocation-api
     */
    static public function getLocation($ip) {
        $ch = curl_init('http://ipwhois.app/json/' . $ip);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        $ipWhoIsResponse = json_decode($json, true);
        return "{$ipWhoIsResponse['city']}, {$ipWhoIsResponse['region']} - ({$ipWhoIsResponse['country']})";
    }
}