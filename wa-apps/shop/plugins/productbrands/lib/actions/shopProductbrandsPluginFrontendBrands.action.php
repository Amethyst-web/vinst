<?php

class shopProductbrandsPluginFrontendBrandsAction extends shopFrontendAction
{
    public function execute()
    {
        if ($t = wa()->getSetting('template_brands', '', array('shop', 'productbrands'))) {
            $template = 'string:'.$t;
        } else {
            $template = 'file:'.wa()->getAppPath('plugins/productbrands/templates/', 'shop').'frontendBrands.html';
        }

        $brands = shopProductbrandsPlugin::getBrands();
        $this->view->assign('brands', $brands);

        $title = wa()->getSetting('brands_name', '', array('shop', 'productbrands'));
        if (!$title) {
            $title = _w('Brands');
        }

        $this->setThemeTemplate('page.html');

        $this->view->assign('page', array(
            'id' => 'brands',
            'title' => $title,
            'name' => $title,
            'content' => $this->view->fetch($template)
        ));

        $this->getResponse()->setTitle($title);

        waSystem::popActivePlugin();
    }
}