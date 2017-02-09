OpenAdvert
============

Advertisement platform. _Currently not maintained_

Installation
------------

### Dependencies

* [PHP-VAST](https://github.com/sokil/php-vast)
* [PHPMongo](https://github.com/sokil/php-mongo)
* [PHPMongo Migrator](https://github.com/sokil/php-mongo-migrator)

### Libux packages:

* mongodb
* ffmpeg

### PHP modules

* GeoIP
```
wget http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
gunzip GeoLiteCity.dat.gz
sudo mkdir -v /usr/share/GeoIP
sudo mv -v GeoLiteCity.dat /usr/share/GeoIP/GeoIPCity.dat
sudo apt-get install php5-geoip
```
* get_browser
```
http://tempdownloads.browserscap.com/stream.asp?Full_PHP_BrowscapINI
```
