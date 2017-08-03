<?php

namespace SiteWideColoring\Service;

use SiteWideColoring\App\Service\AbstractService;

class Settings extends AbstractService
{
    const PARAGRAPH_LAST = 0xff;

    protected $_settings = [
        'sitewide_box' => 'array',
        'sitewide_slugs' => 'string',
        'sitewide_page_ids' => 'string',
        'sitewide_display_rules' => 'string',
    ];

    public function getPositions()
    {
        return [
            'Above',
            'Below',
            'Left',
            'Right'
        ];
    }

    public function getParagraphLocations()
    {
        return [
            1 => '1st paragraph',
            2 => '2nd paragraph',
            3 => '3rd paragraph',
            4 => '4th paragraph',
            5 => '5th paragraph',
            self::PARAGRAPH_LAST => 'last paragraph'
        ];
    }

    public function getDisplayRules()
    {
        return [
            'everywhere' => 'Show on all pages and posts',
            'all_pages' => 'Show on all pages',
            'all_posts' => 'Show on all posts',
            'exclude_home' => 'Show everywhere except Home page',
            'if_page_exclude_id' => 'Show on pages, excluding the following ID(s):',
            'if_post_exclude_id' => 'Show on posts, excluding the following ID(s):',
            'if_post_and_page_exclude_id' => 'Show on pages and posts, excluding the following ID(s):',
            'on_special_id' => 'Show on following ID(s):',
            'on_pages_with_special_id' => 'Show on pages with following ID(s):',
            'on_posts_with_special_id' => 'Show on posts with following ID(s):',
            'on_pages_with_special_id_and_parentid' => 'Show on pages with following ID(s) and parent ID(s):',
        ];
    }

    public function validateOptions(array $data = [])
    {
        $desktopBanners = $data['desktop_content'];
        $mobileBanners = $data['mobile_content'];
        $injectPositions = $data['position'];
        $injectParagraphs = $data['paragraph'];
        $pagesIds = null;

        if (isset($data['sitewide_page_ids'])) {
            $pagesIds = $data['sitewide_page_ids'];
        }

        $banners = array_filter(array_map(
            function($index) use ($desktopBanners, $mobileBanners,
                $injectPositions, $injectParagraphs
            ) {
                if (!empty($desktopBanners[$index]) || !empty($mobileBanners[$index])) {
                    return [
                        'desktop_content' => $desktopBanners[$index],
                        'mobile_content' => $mobileBanners[$index],
                        'position' => $injectPositions[$index],
                        'paragraph' => $injectParagraphs[$index],
                    ];
                }

                return null;
            },
            array_keys(
                $injectPositions
            )
        ));

        return [
            'sitewide_box' => $banners,
            'sitewide_page_ids' => $pagesIds,
            'sitewide_slugs' => $data['sitewide_slugs'],
            'sitewide_display_rules' => $data['sitewide_display_rules'],
        ];
    }

    public function getOptions()
    {
        $options = array_keys($this->_settings);

        return array_combine($options, array_map(
            [$this, 'getOption'],
            $options
        ));
    }

    public function setOptions(array $options = [])
    {
        array_walk($options, function($value, $name) {
            $this->setOption($name, $value);
        });

        return $this;
    }

    public function getOption($name)
    {
        $value = get_option($name, false);

        return $this->_unserializeOption($name, $value);
    }

    public function setOption($name, $value)
    {
        $value = $this->_serializeOption($name, $value);

        if (false === add_option($name, $value)) {
            update_option($name, $value);
        }

        return $this;
    }

    protected function _serializeOption($name, $value)
    {
        $type = $this->_settings[$name];

        switch ($type) {
            case 'int':
                $value = intval($value);
                break;
            case 'float':
                $value = floatval($value);
                break;
            case 'string':
                $value = (string) $value;
                break;
            case 'object':
            case 'array':
                $value = json_encode($value);
                break;
            case 'boolean':
                $value = $value ? 1 : 0;
                break;
        }

        return $value;
    }

    protected function _unserializeOption($name, $value)
    {
        $type = $this->_settings[$name];

        switch ($type) {
            case 'int':
                $value = intval($value);
                break;
            case 'float':
                $value = floatval($value);
                break;
            case 'string':
                $value = (string) $value;
                break;
            case 'object':
                $value = json_decode($value);
                break;
            case 'array':
                $value = json_decode($value, true);
                break;
            case 'boolean':
                $value = !!$value;
                break;
        }

        return $value;
    }
}