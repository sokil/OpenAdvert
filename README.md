Requirements
============

Packages:

* ffmpeg

PHP modules
-----------

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

Crontab
-------
* Add user
```
sudo pw useradd ad -d /nonexistent -s /usr/sbin/nologin -G www
```