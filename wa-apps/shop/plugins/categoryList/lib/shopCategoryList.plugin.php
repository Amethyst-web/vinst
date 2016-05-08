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
//    PHP7
//    public static function getCategories(): string {
    public static function getCategories() {
        $settings = (new self(static::$selfInfo))->getSettings();
        if(!$settings['active']){
            return '';
        }
        $categoryModel = new shopCategoryModel();
        $categories = $categoryModel->query('SELECT id, url, full_url, name, parent_id, count, depth FROM '.$categoryModel->getTableName().' WHERE status = 1');
        $view = wa()->getView();
        $view->assign('categories', $categories);
        return $view->fetch('wa-apps/'.static::$selfInfo['app_id'].'/plugins/'.static::$selfInfo['id'].'/templates/default.html');
    }
}