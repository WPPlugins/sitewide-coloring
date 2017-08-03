<?php

namespace SiteWideColoring\App\Utility;

final class Html
{
    public static function loadDocument($content)
    {
        libxml_use_internal_errors(true);

        $document = new \DOMDocument();
        $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'utf-8'));

        libxml_use_internal_errors(false);

        return $document;
    }

    public static function renderDocument(\DOMDocument $document)
    {
        libxml_use_internal_errors(true);

        $replacements = [
            '/<!DOCTYPE\s.*dtd">/',
            '/<\/?html>/',
            '/<\/?body>/'
        ];

        $content = preg_replace($replacements, '', $document->saveHTML());

        libxml_use_internal_errors(false);

        return $content;
    }

    public static function getBodyElement(\DOMDocument $document)
    {
        return $document->getElementsByTagName('body')->item(0);
    }

    public static function hasEmptyContent($nodeOrHtmlContent)
    {
        if ($nodeOrHtmlContent instanceof \DOMNode) {
            $nodeOrHtmlContent = $nodeOrHtmlContent->textContent;
        }

        return !trim(html_entity_decode($nodeOrHtmlContent), " \t\n\r\0\x0B\xA0");
    }
}