<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: IPInfo.php
| Author: RobiNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/

use GeoIp2\Database\Reader;

class IPInfo {
    /**
     * @param $ip
     *
     * @return \GeoIp2\Model\Asn|string
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function getAsnInfo($ip) {
        if (file_exists(LOGS.'includes/GeoLite2/GeoLite2-ASN.mmdb')) {
            $reader = new Reader(LOGS.'includes/GeoLite2/GeoLite2-ASN.mmdb');

            try {
                return $reader->asn($ip);
            } catch (\Exception $e) {
                return $e->getMessage();
            }

        }

        return NULL;
    }

    /**
     * @param $ip
     *
     * @return \GeoIp2\Model\City|string
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    function getCityInfo($ip) {
        if (file_exists(LOGS.'includes/GeoLite2/GeoLite2-City.mmdb')) {
            $reader = new Reader(LOGS.'includes/GeoLite2/GeoLite2-City.mmdb');

            try {
                return $reader->city($ip);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        return NULL;
    }

    /**
     * @param $ip
     *
     * @return \GeoIp2\Model\Country|string
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    function getCountryInfo($ip) {
        if (file_exists(LOGS.'includes/GeoLite2/GeoLite2-Country.mmdb')) {
            $reader = new Reader(LOGS.'includes/GeoLite2/GeoLite2-Country.mmdb');
            try {
                return $reader->country($ip);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        return NULL;
    }

    /**
     * @param $ip
     *
     * @return mixed
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    function getIPInfo($ip) {
        $asn = $this->getAsnInfo($ip);
        $city = $this->getCityInfo($ip);
        $country = $this->getCountryInfo($ip);
        $data = [];

        if (is_object($asn)) {
            $data['asn'] = [
                'asn'      => 'AS'.$asn->autonomousSystemNumber,
                'network'  => $asn->network,
                'org'      => $asn->autonomousSystemOrganization,
                'hostname' => gethostbyaddr($ip)
            ];
        }

        if (is_object($city)) {
            $data['city'] = [
                'name'     => $city->city->name,
                'loc'      => [
                    'lat' => $city->location->latitude,
                    'lon' => $city->location->longitude
                ],
                'postal'   => $city->postal->code,
                'timezone' => $city->location->timeZone
            ];
        }

        if (is_object($country)) {
            $data['country'] = [
                'name'     => $country->country->name,
                'iso_code' => $country->country->isoCode
            ];
        }

        if (empty($data)) return NULL;

        return $this->cacheData($data, str_replace('.', '-', $ip));
    }

    /**
     * @param      $data
     * @param      $file_name
     *
     * @return mixed
     */
    private function cacheData($data, $file_name) {
        $file = LOGS.'cache/'.$file_name.'.cache';
        $cache_time = 2678400; // One month
        $file_time = 0;

        if (file_exists($file)) {
            $file_time = filemtime($file);
        }

        $filetimemod = $file_time + $cache_time;

        if ($filetimemod < time()) {
            if ($data) {
                file_put_contents($file, serialize($data));
            }
        } else {
            $data = unserialize(file_get_contents($file));
        }

        return $data;
    }
}
