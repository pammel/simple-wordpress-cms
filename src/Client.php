<?php

namespace pammel\SimpleWordpressCms;

use Symfony\Component\DomCrawler\Crawler;

class Client
{
    /**
     * @var ClientConfig
     */
    private $config;

    public function __construct(ClientConfig $config)
    {
        $this->config = $config;
    }

    public function getPage(string $slug): Page
    {
        # get CSS
        $crawler = new Crawler(file_get_contents($this->config->getBlogUrl() . '/' . $slug));
        $cssFiles = $crawler->filter('head link[rel=stylesheet]')->extract(['href']);

        # get HTML
        $body = '[404] Page not exists';
        $content = json_decode(file_get_contents($this->config->getBlogUrl() . '/wp-json/wp/v2/pages/?slug='.$slug), true);
        if(isset($content[0]['content']['rendered'])){
            $body = $content[0]['content']['rendered'];
        }

        return (new Page())
           ->setCssFiles($cssFiles)
           ->setBody($body)
           ;
    }

}