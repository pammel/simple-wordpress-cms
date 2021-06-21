<?php

namespace pammel\SimpleWordpressCms;

use Symfony\Component\DomCrawler\Crawler;

class Page implements PageInterface
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
     * @var ?Error[]
     */
    private $errors;

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
    private $wpHeadMeta;

    /**
     * @var string
     */
    private $htmlBody;

    /**
     * @var string
     */
    private $htmlHead;

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

    /**
     * @return ?Error[]
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    /**
     * @param ?Error[] $errors
     */
    public function setErrors(?array $errors): Page
    {
        $this->errors = $errors;
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
        $this->getHtmlHead();
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
        if ($this->htmlBody === null) {
            $html = $this->getWpContent();

            if($this->config->isAutoConvertWordpressUrlIntoProjectUrl()){
                $html = $this->convertWordpressUrlIntoProjectUrl($html);
            }

            if ($pregReplace = $this->config->getHtmlBodyPregReplace()) {
                $html = $this->pregReplace($html, $pregReplace);
            }

            $this->htmlBody = str_replace(['<%wpContent%>', '<%cssMergedFile%>'], [$html, $this->getCssMergedFileUrl()], $this->config->getHtmlBodyTemplate());
        }

        return $this->htmlBody;
    }

    public function getHtmlHead(): string
    {
        if ($this->htmlHead === null) {
            $html = [];
            if($this->getWpHeadMeta()){
                $html = $this->getWpHeadMeta()->each(function(Crawler $node, $i){
                    return $node->outerHtml();
                });
            }

            $this->htmlHead = implode("\n", $html);

            if($this->config->isAutoConvertWordpressUrlIntoProjectUrl()){
                $this->htmlHead = $this->convertWordpressUrlIntoProjectUrl($this->htmlHead);
            }

            if ($pregReplace = $this->config->getHtmlHeadPregReplace()) {
                $this->htmlHead = $this->pregReplace($this->htmlHead, $pregReplace);
            }
        }

        return $this->htmlHead;
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

        if($this->errors){
            return sprintf('<div class="swpcms-error">%s</div>%s', $this->errors[0]->getMessage(), $this->wpContent);
        }

        return $this->wpContent;
    }

    public function getWpHeadMeta(): ?Crawler
    {
        if(!$this->wpHeadMeta){
            if($this->crawler && $this->config->getHtmlHeadSelector()){
                $this->wpHeadMeta = $this->crawler->filter($this->config->getHtmlHeadSelector());
            }
            else{
                $this->wpHeadMeta = null;
            }
        }

        return $this->wpHeadMeta;
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

        $localPrefix = '(?:\/[a-z]{2})';

        return [
           '~'.$this->escapeRegexUrl($wordpressUrl).'(?=[\p{L}0-9\/\._-]{1,}\.(jpe?g|gif|png))~sim' => $preserveImgUrl,
           '~'.$this->escapeRegexUrl($wordpressUrl).$localPrefix.'(?=[\p{L}0-9\/\._-]{1,})~sim' => $projectUrl,
           '~'.$this->escapeRegexUrl($preserveImgUrl).'(?=[\p{L}0-9\/\._-]{1,}\.(jpe?g|gif|png))~sim' => $wordpressUrl,
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