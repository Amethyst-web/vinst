<?php
/**
 * Created by PhpStorm.
 * User: Nikita
 * Date: 05.03.2016
 * Time: 21:35
 */

class shopCategoryListPlugin extends shopPlugin{
    protected static $selfInfo = array(
        'id' => 'categoryList',
        'app_id' => 'shop'
    );

    /**
     * Возвращает html-код категорий
     * @return mixed|string
     */
    public static function getCategories() {
        $settings = (new self(static::$selfInfo))->getSettings();
        if(!$settings['active']){
            return '';
        }
        $categories = new shopCategories();
        $view = wa()->getView();
        $view->assign('categories', $categories->getList());
        return $view->fetch(static::getPluginTemplatePath());
    }
}