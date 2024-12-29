<?php

namespace DLPL\Prelaunch;

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class Scraper
{

    public function get($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $links = [];

        $parsed_url = parse_url($url);
        $base_domain = $parsed_url['host'];

        $client = new Client(array(
            'verify' => false
        ));
        $response = $client->request("GET", $url);
        $html = $response->getBody();

        if ($html === false) {
            return false;
        }
        
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        libxml_clear_errors();

        do_action('dlpl_before_scrape', $dom, $url);

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        foreach ($dom->getElementsByTagName('a') as $anchor) {
            $href = $anchor->getAttribute('href');
            if (filter_var($href, FILTER_VALIDATE_URL)) {
                $link_host = parse_url($href, PHP_URL_HOST);
                if ($link_host === $base_domain) {
                    $links[] = $href;
                }
            }
        }

        return array_values(array_unique($links));
    }
}
