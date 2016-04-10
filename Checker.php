<?php

require_once (__DIR__ . '/vendor/autoload.php');

/**
 * @author bootjp
 */
class Checker
{
    protected $client;

    protected static $IpAddress;

    /**
     * initialisation.
     */
    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
                'defaults' => [
                    'exceptions' => false,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) ' .
                        'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36'
                    ]
                ]
            ]
        );

        self::$IpAddress = parse_ini_file(__DIR__ . '/ipaddress.ini', true);
    }

    /**
     * @param string $url [optional]
     * @return array
     */
    public function fetch($url = 'http://support.sakura.ad.jp/mainte/mainteindex.php?service=vps')
    {
        $error = '';
        $existsIpAddress = [];

        try {
            $response = $this->client->get($url);

            preg_match_all('{/mainte/mainteentry.php\?id=(?<page>.+?)"}s', $response->getBody()->getContents(), $matches);
            foreach ($matches['page'] as $page) {
                $pageUrl = "http://support.sakura.ad.jp/mainte/mainteentry.php?id={$page}";
                $contents = $this->client->get($pageUrl)->getBody()->getContents();
                preg_match_all('#(?<ipaddress>[0-9]+.[0-9]+.[0-9]+.[0-9]+?)#', $contents, $matches);

                $onPageIpAddress = $matches['ipaddress'];

                $result = array_filter(self::$IpAddress['static'], function ($ipAddress) use ($onPageIpAddress) {
                    return in_array($ipAddress, $onPageIpAddress);
                });
                if (count($result) !== 0) {
                    $existsIpAddress['static'][$pageUrl] = $result;
                }

                $result = array_filter(self::$IpAddress['regexp'], function ($ipAddress) use ($onPageIpAddress) {
                    return preg_grep($ipAddress, $onPageIpAddress);
                });
                if (count($result) !== 0) {
                    $existsIpAddress['regexp'][$pageUrl] = $result;
                }

                usleep(500000);
            }


        } catch (\Exception $e) {
            $error .= "\n {$url}\t {$e->getMessage()}";
        }

        if ($error !== '') {
            var_export($error);
        }

        return $existsIpAddress;
    }
}
