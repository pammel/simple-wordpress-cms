<?php

namespace pammel\SimpleWordpressCms;

use Symfony\Component\DomCrawler\Crawler;

class Page
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * @var array
     */
    private $apiResult;

    /**
     * @var array
     */
    private $error;

    /**
     * @var string
     */
    private $wpContent;

    /**
     * @var array
     */
    private $wpCssFiles = [];

    /**
     * @var string
     */
    private $cssMergedFilenameWithHash;

    /**
     * @var ?Crawler
     */
    private $wpHeaderMeta;

    /**
     * @var string
     */
    private $bodyHtml;

    /**
     * @var string
     */
    private $headerHtml;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $canonical;

    /**
     * @var string
     */
    private $robots;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function setCrawler(?Crawler $crawler): Page
    {
        $this->crawler = $crawler;
        return $this;
    }

    public function setApiResult(?array $apiResult): Page
    {
        $this->apiResult = $apiResult;
        return $this;
    }

    public function setError(?array $error): Page
    {
        $this->error = $error;
        return $this;
    }

    /**
     * Preprocess all values. This is useful if you cache the Page object
     *
     * @return $this
     */
    public function preprocess(): Page
    {
        $this->getHtmlBody();
        $this->getHtmlHeader();
        $this->getTitle();
        $this->getDescription();
        $this->getCanonical();
        $this->getRobots();

        return $this;
    }

    public function getTitle(): string
    {
        if(!$this->title){
            if($this->crawler){
                $this->title = $this->crawler->filter('title')->text();
            }
            else{
                $this->title = '';
            }
        }

        return $this->title;
    }

    public function getDescription(): string
    {
        if(!$this->description){
            if($this->crawler){
                $desc = $this->crawler->filter('meta[name=description]')->extract(['content']);
                $this->description = isset($desc[0]) ? $desc[0] : '';
            }
            else{
                $this->description = '';
            }
        }

        return $this->description;
    }

    public function getCanonical(): string
    {
        if($this->canonical === null){
            if($this->crawler){
                $href = $this->crawler->filter('link[rel=canonical]')->extract(['href']);
                $this->canonical = isset($href[0]) ? $href[0] : '';
            }
            else{
                $this->canonical = '';
            }
        }

        return $this->canonical;
    }

    public function getRobots(): string
    {
        if($this->robots === null){
            if($this->crawler){
                $cont = $this->crawler->filter('meta[name=robots]')->extract(['content']);
                $this->robots = isset($cont[0]) ? $cont[0] : '';
            }
            else{
                $this->robots = '';
            }
        }

        return $this->robots;
    }

    public function getHtmlBody(): string
    {
        if ($this->bodyHtml === null) {
            $html = $this->getWpContent();

            if($this->config->isAutoConvertWordpressUrlIntoProjectUrl()){
                $html = $this->convertWordpressUrlIntoProjectUrl($html);
            }

            if ($pregReplace = $this->config->getHtmlBodyPregReplace()) {
                $html = $this->pregReplace($html, $pregReplace);
            }

            $this->bodyHtml = str_replace(['<%wpContent%>', '<%cssMergedFile%>'], [$html, $this->getCssMergedFileUrl()], $this->config->getHtmlBodyTemplate());
        }

        return $this->bodyHtml;
    }

    public function getHtmlHeader()
    {
        if ($this->headerHtml === null) {
            $html = [];
            if($this->getWpHeaderMeta()){
                $html = $this->getWpHeaderMeta()->each(function(Crawler $node, $i){
                    return $node->outerHtml();
                });
            }

            $this->headerHtml = implode("\n", $html);

            if($this->config->isAutoConvertWordpressUrlIntoProjectUrl()){
                $this->headerHtml = $this->convertWordpressUrlIntoProjectUrl($this->headerHtml);
            }

            if ($pregReplace = $this->config->getHtmlHeaderPregReplace()) {
                $this->headerHtml = $this->pregReplace($this->headerHtml, $pregReplace);
            }
        }

        return $this->headerHtml;
    }

        /**
     * @return string
     */
    public function getWpContent(): string
    {
        if(!$this->wpContent){
            if(isset($this->apiResult[0]['content']['rendered'])){
                $this->wpContent = $this->apiResult[0]['content']['rendered'];
            }
        }

        if($this->error){
            return sprintf('<div class="swpcms-error">%s</div>%s', implode('<br>', $this->error), $this->wpContent);
        }

        return $this->wpContent;
    }

    public function getWpHeaderMeta(): ?Crawler
    {
        if(!$this->wpHeaderMeta){
            if($this->crawler && $this->config->getHtmlHeaderSelector()){
                $this->wpHeaderMeta = $this->crawler->filter($this->config->getHtmlHeaderSelector());
            }
            else{
                $this->wpHeaderMeta = null;
            }
        }

        return $this->wpHeaderMeta;
    }

    /**
     * get wordpress css files
     *
     * @return array
     */
    public function getWpCssFiles(): array
    {
        if(!$this->wpCssFiles){
            if($this->crawler){
                $this->wpCssFiles = $this->crawler->filter('head link[rel=stylesheet]')->extract(['href']);
            }
            else{
                $this->wpCssFiles = [];
            }
        }

        return $this->wpCssFiles;
    }

    /**
     * get all css files (wordpress and additional)
     *
     * @return array
     */
    public function getCssFiles(): array
    {
        return array_merge($this->getWpCssFiles(), $this->config->getCssFilesAdditional());
    }

    private function mergeCssFiles()
    {
        if(!file_exists($this->getCssMergedFileLocal())) {

            # Clean up old files
            $files = glob($this->config->getCssFolderLocal() . '/' . explode('.', $this->getCssMergedFilenameWithHash())[0] . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            # merge css files
            $cssMerged = '';
            foreach ($this->getCssFiles() as $file) {
                $css = file_get_contents($file);
                if ($pregReplace = $this->config->getCssPregReplace()) {
                    $css = $this->pregReplace($css, $pregReplace);
                }

                $cssMerged .=
                   "/* " . str_repeat('-', 80) . " */ \n" .
                   "/* " . basename($file) . "*/ \n" .
                   "/* " . str_repeat('-', 80) . " */ \n" .
                   $css . "\n\n";
            }

            file_put_contents($this->getCssMergedFileLocal(), trim($cssMerged));
        }
    }

    public function getCssMergedFileLocal(): string
    {
        return $this->config->getCssFolderLocal() . '/' . $this->getCssMergedFilenameWithHash();
    }

    public function getCssMergedFileUrl(): string
    {
        return $this->config->getCssFolderPublicUrl() . '/' . $this->getCssMergedFilenameWithHash();
    }

    private function getCssMergedFilenameWithHash(): string
    {
        if($this->cssMergedFilenameWithHash === null){
            $hash = md5(serialize($this->getCssFiles()));

            if(strpos($this->config->getCssMergedFilename(), '.') !== false){
                $parts = explode('.', $this->config->getCssMergedFilename());
                array_splice( $parts, count($parts)-1, 0, $hash );
                $this->cssMergedFilenameWithHash = implode('.', $parts);
            }
            else{
                $this->cssMergedFilenameWithHash = $this->config->getCssMergedFilename() . '.' . $hash;
            }

            $this->mergeCssFiles();
        }

        return $this->cssMergedFilenameWithHash;
    }

    /**
     * convert all Wordpress-URLs into Project-URLs except image-URLs.
     *
     * @param string $str
     * @return string
     */
    public function convertWordpressUrlIntoProjectUrl(string $str): string
    {
        return $this->pregReplace($str, $this->getRegexConvertUrl());
    }

    /**
     * regex that replace blogUrls into projectUrls (except imageUrls)
     */
    private function getRegexConvertUrl(): array
    {
        $wordpressUrl = $this->config->getWordpressUrl();
        $projectUrl = $this->config->getProjectUrl();
        $preserveImgUrl = str_replace('://', '::////', $wordpressUrl);

        return [
           '~'.$this->escapeRegexUrl($wordpressUrl).'(?=[a-zA-Z0-9\/\._-]{1,}\.(jpe?g|gif|png))~sim' => $preserveImgUrl,
           '~'.$this->escapeRegexUrl($wordpressUrl).'(?=[a-zA-Z0-9\/\._-]{1,})~sim' => $projectUrl,
           '~'.$this->escapeRegexUrl($preserveImgUrl).'(?=[a-zA-Z0-9\/\._-]{1,}\.(jpe?g|gif|png))~sim' => $wordpressUrl,
        ];
    }

    private function escapeRegexUrl(string $url): string
    {
        return str_replace([':', '/', '.'], ['\:', '\/', '\.'], $url);
    }

    private function pregReplace(string $str, array $pregReplace): string
    {
        foreach ($pregReplace AS $pattern => $replacement){
            $str = preg_replace($pattern, $replacement, $str);
        }

        return $str;
    }
}