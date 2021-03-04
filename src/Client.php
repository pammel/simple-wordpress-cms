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
            $error[] = sprintf('%s error getting CSS-files<br>[%d] %s', self::NAME, $e->getCode(), $e->getMessage());
        }

        try{
            $apiResult = json_decode(file_get_contents($this->config->getWordpressUrl() . '/wp-json/wp/v2/pages/?slug='.$slug), true);
        }
        catch(\Throwable $e){
            $apiResult = null;
            $error[] = sprintf('%s error getting content<br>[%d] %s', self::NAME, $e->getCode(), $e->getMessage());
        }

        return (new Page($this->config))
           ->setCrawler($crawler)
           ->setApiResult($apiResult)
           ->setError($error)
           ;
    }
}