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
        $exceptions = [];

        try{
            $crawler = new Crawler(file_get_contents($this->config->getWordpressUrl() . '/' . $slug));
        }
        catch(\Throwable $e){
            $crawler = null;
            $exceptions[] = new Exception($e->getMessage(), $e->getCode());
        }

        try{
            $apiResult = json_decode(file_get_contents($this->config->getWordpressUrl() . '/wp-json/wp/v2/pages/?slug='.$slug), true);
        }
        catch(\Throwable $e){
            $apiResult = null;
            $exceptions[] = new Exception($e->getMessage(), $e->getCode());
        }

        return (new Page($this->config))
           ->setCrawler($crawler)
           ->setApiResult($apiResult)
           ->setExceptions($exceptions)
           ;
    }
}