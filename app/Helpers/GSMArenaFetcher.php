<?php

namespace App\Helpers;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler as Crawler;
use App\Helpers\Contracts\DeviceFetcherContract;

class GSMArenaFetcher implements DeviceFetcherContract
{
    /**
     * @var Client
     */
    protected $client = null;
    /**
     * @var GuzzleClient
     */
    protected $guzzleClient = null;
    /**
     * @var Crawler
     */
    protected $crawler = null;

    const URL = "http://www.gsmarena.com/";

    public function __construct()
    {
        $this->client = new Client();

        $guzzleClient = new GuzzleClient(array(
            'timeout' => 60,
        ));

        $this->client->setClient($guzzleClient);

        // Visit home page
        $this->crawler = $this->client->request('GET', self::URL);
    }

    /**
     * Fetch brand names
     * @return array
     */
    public function getBrands()
    {
        $brands = array();

        $this->crawler->filter('.brandmenu-v2 li')->each(function (Crawler $node, $i) use (&$brands) {
            $brands[$node->text()] = self::URL . $node->children()->first()->attr('href');
        });

        return $brands;
    }

    public function getAll()
    {
        $devices = array();
        $this->crawler = $this->visitHomePage();
        $page_count = 1;

        while (true) {
            // Get all links from page
            $this->crawler->filter('.makers li')->each(function (Crawler $node, $i) use (&$devices) {
                $name = $node->filter('span')->first()->text();
                $url = $node->children()->first()->attr('href');
                $image = $node->filter('img')->first()->attr('src');
                $description = $node->filter('img')->first()->attr('title');
                $dev = array('name' => $name, 'url' => $url, 'image' => $image, 'description' => $description);
                $devices[] = $dev;
            });
            // go to next page
            $nextPageElem = $this->crawler->filter('a.pages-next')->first();
            if ( strstr( $nextPageElem->attr('class'), 'disabled' ) == false ) // check if is enabled
                $this->crawler = $this->client->click( $this->crawler->filter('a.pages-next')->first()->link() );
            else
                break;
            $page_count += 1;
        }

        return $devices;
    }

    public function getByBrand($brand_name)
    {
        // TODO: Implement getByBrand() method.
    }
}