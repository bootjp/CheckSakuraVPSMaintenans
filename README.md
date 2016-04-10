# さくらVPSのメンテナンスに情報にIPアドレスが乗っているかしらべるやつ
## Check whether the IP address to the maintenance information page of Sakura VPS exists


[![Build Status](https://travis-ci.org/bootjp/CheckSakuraVPSMaintenans.svg?branch=master)](https://travis-ci.org/bootjp/CheckSakuraVPSMaintenans)

### how to use

```bash
git clone git@github.com:bootjp/CheckSakuraVPSMaintenans.git
cd CheckSakuraVPSMaintenans/
# or 
cd checksakuravpsmaintenans/
composer install
```

and Edit ipaddress.ini

### run of script

```bash
php wrapper.php 
```

### run of docker image (https://hub.docker.com/r/bootjp/checksakuravpsmaintenans/)

```bash
docker run -e ENV_INI="ini file path" -v ini file dir/:/tmp bootjp/checksakuravpsmaintenans 
```