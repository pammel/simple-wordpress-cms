<?php

namespace pammel\SimpleWordpressCms;

use Symfony\Component\DomCrawler\Crawler;

class Client
{
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
        # get CSS
        $crawler = new Crawler(file_get_contents($this->config->getBlogUrl() . '/' . $slug));
        $cssFiles = $crawler->filter('head link[rel=stylesheet]')->extract(['href']);

        # get HTML
        $body = '[404] Page not exists';
        try{
            $content = json_decode(file_get_contents($this->config->getBlogUrl() . '/wp-json/wp/v2/pages/?slug='.$slug), true);
            if(isset($content[0]['content']['rendered'])){
                $body = $content[0]['content']['rendered'];
            }
        }
        catch(\Throwable $e){
            $body = sprintf('[%d] %s', $e->getCode(), $e->getMessage());
        }

        return (new Page($this->config))
           ->setCssFiles($cssFiles)
           ->setBody($body)
           ;
    }
}