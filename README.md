# Purpose

This alternc plugin provide NSS support about alternc account. Purpose is to provide POSIX compatible account :
* could be interessting to run php-fpm service
* no direct acces (no pam authentication)


# Requirement

You need :
* debian server
* alternc >= 3.2


# Get the package #

## Build own package ##

You can compile this package with:

```
    apt install build-essential debhelper git
    git clone https://github.com/AlternC/alternc-php-fpm
    cd alternc-php-fpm
    dpkg-buildpackage -us -uc
```

## From GitHub ##

You can obtain nightly and last stable package from the dedicated page : [releases page](https://github.com/AlternC/alternc-nss/releases)

## From our repository ##

Our stable repository is avalaible at https://debian.alternc.org

```
echo "deb http://debian.alternc.org/ $(lsb_release -cs) main" >> /etc/apt/sources.list.d/alternc.list
wget https://debian.alternc.org/key.txt -O - | apt-key add -
apt update
```

# Configuration and Activation

Once alternc-nss installed, you must :
* run **alternc.install**

You can run also **/usr/lib/alternc/generate_certbot.php** to get faster certificate to all domains hosted.


# Alternative

We provide also another package **alternc-nss-sync** wich provide alternc-nss service. In this case user account are set in defaul files. Packages are available also in GitHub release and our repository.

# ROADMAP

* [x] Provide alternc account in nss (0.0.1)
* [x] Support shadow file (0.0.2)
* [x] Set home directory (0.0.3)
