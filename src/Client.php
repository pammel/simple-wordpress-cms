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

        # get CSS
        $cssFiles = [];
        try{
            $crawler = new Crawler(file_get_contents($this->config->getBlogUrl() . '/' . $slug));
            $cssFiles = $crawler->filter('head link[rel=stylesheet]')->extract(['href']);
        }
        catch(\Throwable $e){
            $error[] = sprintf('%s error getting CSS-files<br>[%d] %s', self::NAME, $e->getCode(), $e->getMessage());
        }

        # get HTML
        $content = '';
        try{
            $data = json_decode(file_get_contents($this->config->getBlogUrl() . '/wp-json/wp/v2/pages/?slug='.$slug), true);
            if(isset($data[0]['content']['rendered'])){
                $content = $data[0]['content']['rendered'];
            }

            if ($strReplace = $this->config->getContentReplace()) {
                $content = str_replace(array_keys($strReplace), array_values($strReplace), $content);
            }
        }
        catch(\Throwable $e){
            $error[] = sprintf('%s error getting content<br>[%d] %s', self::NAME, $e->getCode(), $e->getMessage());
        }

        # Set Error
        if($error){
            $content .= implode('<br>', $error);
        }

        return (new Page($this->config))
           ->setWpCssFiles($cssFiles)
           ->setWpContent($content)
           ;
    }
}