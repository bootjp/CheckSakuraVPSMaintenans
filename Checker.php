<?php

namespace Maintenance;

require_once (__DIR__ . '/vendor/autoload.php');

/**
 * @author bootjp
 */
class Checker
{
    protected $client;

    protected static $ipAddress;

    protected $error = '';

    public function __construct($iniFilePath = null)
    {
        $this->client = new \GuzzleHttp\Client([
                'defaults' => [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) ' .
                        'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36'
                    ]
                ]
            ]
        );
        if (!is_null($iniFilePath)) {
            if (!file_exists($iniFilePath)) {
                throw new \InvalidArgumentException('ini file not found');
            }

            self::$ipAddress = parse_ini_file($iniFilePath, true);
        } else {
            self::$ipAddress = parse_ini_file(__DIR__ . '/ipaddress.ini', true);
        }
    }

    /**
     * @param string $url [optional]
     * @return array
     */
    public function fetch($url = 'http://support.sakura.ad.jp/mainte/mainteindex.php?service=vps')
    {
        $existsIpAddress = [];

        try {
            preg_match_all('{/mainte/mainteentry.php\?id=(?<page>.+?)"}s', $this->client->get($url)->getBody()->getContents(), $matches);
            foreach ($matches['page'] as $page) {
                $pageUrl = 'http://support.sakura.ad.jp/mainte/mainteentry.php?id=' . $page;
                $contents = $this->client->get($pageUrl)->getBody()->getContents();
                preg_match_all('{(?<ipaddress>[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)}s', $contents, $matches);

                $onPageIpAddress = $matches['ipaddress'];

                foreach(array_keys(self::$ipAddress) as $type) {
                    if (array_key_exists($type, self::$ipAddress)) {
                        $result = array_filter(self::$ipAddress[$type], function ($ipAddress) use ($onPageIpAddress) {
                            return in_array($ipAddress, $onPageIpAddress);
                        });
                        if (count($result) !== 0) {
                            $existsIpAddress[$type][$pageUrl] = $result;
                        }
                    }
                }
                usleep(500000);
            }


        } catch (\Exception $e) {
            $this->error .= "{$e->getMessage()}\n";
        }

        return $existsIpAddress;
    }

    public function getException()
    {
        if ($this->error !== '') {
            throw new \RuntimeException($this->error);
        }
    }
}
