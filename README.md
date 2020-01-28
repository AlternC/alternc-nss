# Purpose

This alternc plugin provide NSS support about alternc account. Purpose is to provide POSIX compatible account :
* could be interessting to run php-fpm service
* no direct acces (no pam authentication)


# Requirement

You need :
* debian server (from wheezy to Stretch)
* alternc >= 3.2
* [apt-transport-https](https://packages.debian.org/search?keywords=apt-transport-https) package to use https bintray service.


# Installation

## Stable package

You can download last package from :
* github : [release page](../../releases/latest)
* bintray : [ ![Bintray](https://api.bintray.com/packages/alternc/stable/alternc-nss/images/download.svg) ](https://bintray.com/alternc/stable/alternc-nss/_latestVersion)
* from bintray repository

### With Wheezy, Jessie, Stretch

```shell
apt-get install apt-transport-https
echo "deb [trusted=yes] https://dl.bintray.com/alternc/stable stable main"  >> /etc/apt/sources.list.d/alternc.list
apt-get install alternc-nss
alternc.install
```

## Nightly package

You can get last package from bintray, it's follow git master branch

```shell
echo "deb [trusted=yes] https://dl.bintray.com/alternc/nightly stable main"  >> /etc/apt/sources.list.d/alternc.list
apt-get update
apt-get upgrade
apt-get install alternc-nss
alternc.install
```

# Configuration and Activation

Once alternc-nss installed , you must :
* run **alternc.install**

You can run also **/usr/lib/alternc/generate_certbot.php** to get faster certificate to all domains hosted.

# Packaging from source

To generate package we use [fpm tool](https://github.com/jordansissel/fpm)

```shell
apt-get install ruby ruby-dev rubygems build-essential
gem install --no-ri --no-rdoc fpm

git clone https://github.com/AlternC/alternc-nss
cd alternc-nss
make

```


# ROADMAP

* [x] Provide alternc account in nss (0.0.1)
* [x] Support shadow file (0.0.2)
* [x] Set home directory (0.0.3)
