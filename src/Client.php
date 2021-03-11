<?php

namespace pammel\SimpleWordpressCms;

use Symfony\Component\DomCrawler\Crawler;

class Client
{
    const NAME = 'Simple-Wordpress-CMS';

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getPage(string $slug): Page
    {
        $error = [];

        try{
            $crawler = new Crawler(file_get_contents($this->config->getWordpressUrl() . '/' . $slug));
        }
        catch(\Throwable $e){
            $crawler = null;
            $error[] = new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        try{
            $apiResult = json_decode(file_get_contents($this->config->getWordpressUrl() . '/wp-json/wp/v2/pages/?slug='.$slug), true);
        }
        catch(\Throwable $e){
            $apiResult = null;
            $error[] = new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return (new Page($this->config))
           ->setCrawler($crawler)
           ->setApiResult($apiResult)
           ->setExceptions($error)
           ;
    }
}