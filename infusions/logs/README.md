# Logs

Infusion for tracking all HTTP requests. Used for debugging.

Run `composer install` before use.

Download [GeoLite2](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data) databases into `logs\includes\GeoLite2`:

- GeoLite2-ASN.mmdb
- GeoLite2-Country.mmdb
- GeoLite2-City.mmdb

## Usage

Put this code in your theme.php
```php
if (defined('DB_LOGS') && db_exists(DB_LOGS)) {
    require_once LOGS.'includes/functions.php';

    save_log();
    max_users_online();
}
```
