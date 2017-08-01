<?php
/**
 * Created by PhpStorm.
 * User: 750371433
 * Date: 01/08/2017
 * Time: 17:34
 */

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Carbon\Carbon;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use \Symfony\Component\DomCrawler\Crawler as Crawler;

class DeviceController extends BaseController
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
    /**
     * @var Logger
     */
    protected static $logger = null;

    const URL = "http://www.gsmarena.com/";

    public function __construct() {
        $this->client = new Client();

        $guzzleClient = new GuzzleClient(array(
            'timeout' => 60,
        ));

        $this->client->setClient($guzzleClient);
    }

    public function read($brand = null) {
        //$result = $this->fetchDevicesFromBrand('http://www.gsmarena.com/samsung-phones-9.php');
        $result = $this->fetchBrandList( $this->visitHomePage() );

        return response()->json($result);
    }

    /**
     * @return Crawler
     */
    private function visitHomePage() {
        $this->crawler = $this->client->request('GET', self::URL);
        return $this->crawler;
    }

    /**
     * Gets a list of all brands
     * @param $crawler Crawler
     * @return array list of all brands (as keys) and links (as values)
     */
    private function fetchBrandList($crawler) {
        $elems = array();

        $crawler->filter('.brandmenu-v2 li')->each(function (Crawler $node, $i) use (&$elems) {
            $elems[$node->text()] = self::URL . $node->children()->first()->attr('href');
        });

        return $elems;
    }

    private function fetchDevicesFromBrand($url)
    {
        $devices = array();
        $this->crawler = $this->client->request('GET', $url);
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


}