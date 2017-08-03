<?php

namespace SiteWideColoring\Hook;

use SiteWideColoring\App\Hook\AbstractHook;

class SiteWiding extends AbstractHook
{
   public function filterContentAction($content)
   {
       return $this->registry('service/sitewiding')->filterContent($content);
   }

    public function injectAction($content)
    {
        $service = $this->registry('service/sitewiding');
        if(!$service->isCurrentPageExcluded()
            && $service->isCurrentPageMatchDisplayRules()) {
            return $service->injectToContent($content);
        }

        return $content;
    }
}
