<?php

namespace SiteWideColoring\Service;

use SiteWideColoring\App\Service\AbstractService;
use SiteWideColoring\App\Utility\Html;

class SiteWiding extends AbstractService
{
    public function isCurrentPageExcluded()
    {
        $isExcluded = false;
        $settings = $this->registry('service/settings');
        $excludedPagesUrls = array_filter(array_map(
            'trim',
            preg_split(
                '/[\t\s\r\n,]+/',
                $settings->getOption('sitewide_slugs')
            )
        ));

        if (is_page('privacy-policy') || is_page('cookie-policy')) {
            $isExcluded = true;
        } elseif ($this->registry('detection')->isMobile() && is_page('quote')) {
            $isExcluded = true;
        } else {
            $requestUrl = strtolower($this->registry('request')->getUri());

            foreach ($excludedPagesUrls as $url) {
                if (false !== strpos($requestUrl, strtolower($url))) {
                    $isExcluded = true;

                    break;
                }
            }
        }

        return $isExcluded;
    }

    public function isCurrentPageMatchDisplayRules()
    {
        $settings = $this->registry('service/settings');
        $rule = $settings->getOption('sitewide_display_rules');
        $parentPage = wp_get_post_parent_id(get_the_ID());
        $isSelected = $this->_matchToSelectedPages();
        switch ($rule) {
            case 'everywhere':
                return is_page() || is_single();
            case 'all_pages':
                return is_page();
            case 'all_posts':
                return is_single();
            case 'exclude_home':
                return !is_home() && !is_front_page();
            case 'if_page_exclude_id':
                return is_page() && !$isSelected;
            case 'if_post_exclude_id':
                return is_single() && !$isSelected;
            case 'if_post_and_page_exclude_id':
                return (is_single() || is_page()) &&  !$isSelected;
            case 'on_special_id':
                return $isSelected;
            case 'on_pages_with_special_id':
                return is_page() && $isSelected;
            case 'on_posts_with_special_id':
                return is_single() && $isSelected;
            case 'on_pages_with_special_id_and_parentid':
                return is_page() && ($isSelected || $this->_matchToSelectedPages($parentPage));
        }
    }

    public function injectToContent($content)
    {
        $banners  = $this->registry('service/settings')->getOption('sitewide_box');
        $content  = do_shortcode($content);
        $contentDom  = Html::loadDocument($content);
        $paragraphs  = $this->_splitToParagraphs(Html::getBodyElement($contentDom));
        $paragraphsAmount = count($paragraphs);

        if (!$paragraphsAmount) {
            return $content;
        }

        foreach ($paragraphs as $paragraphIndex => $paragraph) {
            if (!Html::hasEmptyContent($paragraph)) {
                foreach ($banners as $bannerOptions) {
                    $currentParagraphIndex = $paragraphIndex + 1;

                    if ($bannerOptions['paragraph'] == Settings::PARAGRAPH_LAST) {
                        $bannerParagraph = $paragraphsAmount;
                    } else {
                        $bannerParagraph = intval($bannerOptions['paragraph']);
                    }
                    if ($currentParagraphIndex != $bannerParagraph) {
                        continue;
                    }

                    if (false !== ($banner = $this->_renderBanner($bannerOptions))) {
                        $injectMethod = '_inject' . $bannerOptions['position'];

                        if (!method_exists($this, $injectMethod)) {
                            $injectMethod = '_injectToDefaultPosition';
                        }

                        $this->$injectMethod([
                            'paragraph' => $paragraph,
                            'injectionParagraphIndex' => $bannerOptions['paragraph'],
                            'injection' => $banner
                        ]);
                    }
                }
            }
        }

        return Html::renderDocument($contentDom);
    }

    protected function _injectBelow(array $options)
    {
        $paragraph = $options['paragraph'];
        $injection = $options['injection'];
        $injectionParagraphIndex = $options['injectionParagraphIndex'];
        $bannerContent = $this->_wrapBanner($injection);
        $contentDom = $paragraph->ownerDocument;
        $contentBody = Html::getBodyElement($contentDom);
        $bannerDom = Html::loadDocument($bannerContent);
        $bannerWrapper = Html::getBodyElement($bannerDom)->firstChild;
        $importedBannerWrapper = $contentDom->importNode($bannerWrapper, true);

        ($injectionParagraphIndex == Settings::PARAGRAPH_LAST)
            ? $contentBody->appendChild($importedBannerWrapper)
            : $this->_safeInject(
                $contentBody,
                $importedBannerWrapper,
                function($injection, $body) use ($paragraph) {
                    $body->insertBefore($injection, $paragraph->nextSibling);
                }
            );
    }

    protected function _injectLeft(array $options)
    {
        $this->_injectFloat($options, 'left');
    }

    protected function _injectRight(array $options)
    {
        $this->_injectFloat($options, 'right');
    }

    protected function _injectToDefaultPosition(array $options)
    {
        $paragraph = $options['paragraph'];
        $injection = $options['injection'];
        $contentDom = $paragraph->ownerDocument;
        $contentBody = Html::getBodyElement($contentDom);
        $bannerContent = $this->_wrapBanner($injection);
        $bannerDom = Html::loadDocument($bannerContent);
        $bannerWrapper = Html::getBodyElement($bannerDom)->firstChild;
        $importedBannerWrapper = $contentDom->importNode($bannerWrapper, true);

        $this->_safeInject(
            $contentBody,
            $importedBannerWrapper,
            function($injection, $body) use ($paragraph) {
                $body->insertBefore($injection, $paragraph);
            }
        );
    }

    protected function _injectFloat(array $options, $direction)
    {
        $paragraph = $options['paragraph'];
        $injection = $options['injection'];
        $contentDom = $paragraph->ownerDocument;
        $contentBody = Html::getBodyElement($contentDom);
        $bannerContent = $this->_wrapBanner($injection, $direction);
        $bannerDom = Html::loadDocument($bannerContent);
        $bannerWrapper = Html::getBodyElement($bannerDom)->firstChild;
        $importedBannerWrapper = $contentDom->importNode($bannerWrapper, true);

        $this->_safeInject(
            $contentBody,
            $importedBannerWrapper,
            function($injection) use ($paragraph) {
                $paragraph->insertBefore($injection, $paragraph->firstChild);
            }
        );
    }

    protected function _safeInject($body, $injection, Callable $onInject)
    {
        try {
            $onInject($injection, $body);
        } catch (\Exception $e) {
            $body->appendChild($injection);
        }
    }

    protected function _splitToParagraphs(\DOMNode $documentBody)
    {
        $rootNodes = $documentBody->childNodes;
        $paragraphs = [];
        foreach ($rootNodes as $node) {
            if (in_array($node->nodeName, ['p', 'ul', 'ol'])) {
                $paragraphs[] = $node;
            }
        }
        return $paragraphs;
    }

    protected function _wrapBanner($banner, $direction = 'center')
    {
        if (('center' !== $direction) && $this->registry('detection')->isMobile()) {
            $direction = 'center';
        }

        return  '<div class="' . $direction . '-sidewide-align">' . $banner . '</div>';
    }

    protected function _renderBanner($banner)
    {
        $bannerMode = $this->registry('detection')->isMobile()
                ? 'mobile'
                : 'desktop';

        if (empty($banner[$bannerMode . '_content'])) {
            return false;
        }

        return '<div class="' . $bannerMode . '_banner">'
            . $banner[$bannerMode . '_content']
            . '</div>';
    }

    public function filterContent($content)
    {
        return preg_replace_callback(
            '/\[ad-([^\]]+)\]/six',
            function ($match) {
                list($shortCode, $adId) = $match;

                if ($adId && function_exists('dfrads')) {
                    return dfrads($adId);
                }

                return $shortCode;

            },
            $content
        );
    }

    protected function _matchToSelectedPages( $page = null)
    {
        $settings = $this->registry('service/settings');
        $pagesIds = array_filter(array_map(
            'trim',
            preg_split(
                '/[\t\s\r\n,]+/',
                $settings->getOption('sitewide_page_ids')
            )
        ));

        if (count($pagesIds)) {
            if (!$page) {
                $page = get_the_ID();
            }
            
            return in_array($page, $pagesIds);
        }

        return false;
    }
}