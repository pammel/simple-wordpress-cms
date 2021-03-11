<?php

namespace pammel\SimpleWordpressCms;

interface PageInterface
{
    /**
     * @return Error[]|null
     */
    public function getErrors(): ?array;

    /**
     * Preprocess all values. This is useful if you cache the Page object
     */
    public function preprocess(): Page;

    /**
     * get title tag content
     */
    public function getTitle(): string;

    /**
     * get description meta tag content
     */
    public function getDescription(): string;

    /**
     * get canonical link href
     */
    public function getCanonical(): string;

    /**
     * get robots meta tag content
     */
    public function getRobots(): string;

    /**
     * get processed html body of wordpress page
     */
    public function getHtmlBody(): string;

    /**
     * get processed html head of wordpress page
     */
    public function getHtmlHead(): string;

    /**
     * get plain html body of wordpress page
     */
    public function getWpContent(): string;

    /**
     * get wordpress css files
     */
    public function getWpCssFiles(): array;

    /**
     * get all css files (wordpress and additional)
     */
    public function getCssFiles(): array;

    public function getCssMergedFileLocal(): string;

    public function getCssMergedFileUrl(): string;

    /**
     * replace all Wordpress URLs with project URLs (except image-URLs) inside a string
     */
    public function convertWordpressUrlIntoProjectUrl(string $str): string;

}