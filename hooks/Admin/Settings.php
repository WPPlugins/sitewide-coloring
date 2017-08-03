<?php

namespace SiteWideColoring\Hook\Admin;

use SiteWideColoring\App\Hook\AbstractHook;

class Settings extends AbstractHook
{
    public function initializeAction()
    {
        add_menu_page(
            'SiteWide Banners',
            'SiteWide Banners',
            'manage_options',
            'sitewide', [
                $this,
                'settingsAction'
        ]);
    }

    public function settingsAction()
    {
        $request = $this->registry('request');
        $service = $this->registry('service/settings');

        if ($request->isPost()) {
            $data = $request->get('sitewide', []);
            if ($validated = $service->validateOptions($data)) {
                $service->setOptions($validated);
            }
        }

        $displayRules = $service->getDisplayRules();
        $paragraphsPosition = $service->getParagraphLocations();
        $positions = $service->getPositions();
        $options = $service->getOptions();
        $view = $this->registry('renderer');

        $view->output('admin/settings', [
            'request' => $request,
            'displayRules' => $displayRules,
            'paragraphsPosition'=> $paragraphsPosition,
            'positions' => $positions,
            'options' => $options
        ]);
    }
}
