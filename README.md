# さくらVPSのメンテナンスに情報にIPアドレスが乗っているかしらべるやつ
## Check whether the IP address to the maintenance information page of Sakura VPS exists


[![Build Status](https://travis-ci.org/bootjp/CheckSakuraVPSMaintenans.svg?branch=master)](https://travis-ci.org/bootjp/CheckSakuraVPSMaintenans)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bootjp/CheckSakuraVPSMaintenans/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bootjp/CheckSakuraVPSMaintenans/?branch=master)

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
php sample.php
```

### run of docker image (https://hub.docker.com/r/bootjp/checksakuravpsmaintenans/)

ex)
```bash
docker run -e ENV_INI="ini file path" -v ini file dir/:/tmp bootjp/checksakuravpsmaintenans php /app/sample.php
docker run -it -e ENV_INI='/tmp/ipaddress.ini' -v $(pwd):/tmp bootjp/checksakuravpsmaintenans  php /app/sample.php
```

run bash script

```
#!/bin/bash
pull_result=$(docker pull bootjp/checksakuravpsmaintenans)
pull_code=$?
run_result=$(docker run --rm -e ENV_INI='/tmp/ipadress.ini' -v /root/:/tmp/ bootjp/checksakuravpsmaintenans php /app/sample.php)
run_code=$?
if [ 0 -ne ${pull_code} ] ;then
  echo ${pull_result} >&2
  exit 1
elif [ 0 -ne ${run_code} ] ;then
  echo ${run_result} >&2
  exit 1
fi
exit 0
```
