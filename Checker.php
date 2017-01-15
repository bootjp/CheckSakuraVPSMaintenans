<?php

namespace SakuraVpsMaintenance;

use GuzzleHttp\Client;

require_once(__DIR__ . '/vendor/autoload.php');

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
        $this->client = new Client([
                'defaults' => [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36'
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
            foreach ($matches['page'] as $pageId) {
                $pageUrl = "http://support.sakura.ad.jp/mainte/mainteentry.php?id={$pageId}";
                if (count($result = $this->parse($this->client->get($pageUrl)->getBody()->getContents())) !== 0) {
                    $existsIpAddress = $result;
                }
                usleep(500000);
            }

        } catch (\Exception $e) {
            $this->error .= "{$e->getMessage()}\n";
        }

        return $existsIpAddress;
    }

    /**
     * @param string $contents
     * @return array
     */
    private function parse($contents)
    {
        preg_match_all('{(?<ipaddress>[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)}s', $contents, $matches);
        $onPageIpAddress = $matches['ipaddress'];

        return array_reduce(array_keys(self::$ipAddress), function($carry, $checkType) use($onPageIpAddress) {
            $result = array_filter(self::$ipAddress[$checkType], function($identifier) use($onPageIpAddress, $checkType) {
                switch ($checkType) {
                    case 'static':
                        return in_array($identifier, $onPageIpAddress);

                    case 'regexp':
                        foreach ($onPageIpAddress as $ipAddress) {
                            if (preg_match($identifier, $ipAddress) === 1) {
                                return true;
                            }
                        }

                        return false;
                    default:
                        throw new \RuntimeException("invalid type {$checkType}");
                }
            });

            if (count($result) !== 0) {
                $carry[$checkType] = $result;
            }

            return $carry;
        }, []);
    }

    public function getException()
    {
        if ($this->error !== '') {
            throw new \RuntimeException($this->error);
        }
    }
}
