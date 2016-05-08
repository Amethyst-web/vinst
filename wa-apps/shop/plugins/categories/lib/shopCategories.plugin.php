<?php
/**
 * Created by PhpStorm.
 * User: Nikita
 * Date: 05.03.2016
 * Time: 21:35
 */

class shopCategoriesPlugin extends shopPlugin{
    protected static $selfInfo = array(
        'id' => 'categories',
        'app_id' => 'shop'
    );
    
    public static function GetCategories(){
        $plugin = new self(static::$selfInfo);
        $settings = $plugin->getSettings();
        if(!$settings['active']){
            return '';
        }
        $model = new shopCategoryModel();
        $catResult = $model->query('SELECT * FROM '.$model->getTableName().' ORDER BY depth DESC');
        $result = array();
        while($res = $catResult->fetchAssoc()){
            $result[$res['id']] = $res;
        }
        $categories = $result;
        foreach ($result as $cat){
            if($cat['depth'] != '0'){
                if(!isset($categories[$cat['parent_id']]['subcats'])){
                    $categories[$cat['parent_id']]['subcats'] = array();
                }
                $categories[$cat['parent_id']]['subcats'][] = $cat['id'];
                if(isset($categories[$cat['id']]['subcats'])){
                    $categories[$cat['parent_id']]['subcats'] += $categories[$cat['id']]['subcats'];
                }
                unset($categories[$cat['id']]);
            } else {
                if(!isset($categories[$cat['id']]['subcats'])){
                    $categories[$cat['id']]['subcats'] = array();
                }
                $categories[$cat['id']]['subcats'][] = $cat['id'];
            }
        }
//        die(var_dump($categories));
        foreach($categories as $cat){
            $categories[$cat['id']]['ex_product'] = $model->query('
                SELECT p.*, COUNT(i.product_id) as buyings 
                FROM shop_product p LEFT JOIN shop_order_items i ON p.id = i.product_id 
                WHERE p.`status` = 1 AND p.category_id IN ('.join(',',$cat['subcats']).')
                GROUP BY p.id, p.name
                ORDER BY p.name, buyings ASC
                LIMIT 1
            ')->fetchAssoc();
        }
        $view = wa()->getView();
        $view->assign('categories', $categories);
        return $view->fetch('wa-apps/'.static::$selfInfo['app_id'].'/plugins/'.static::$selfInfo['id'].'/templates/default.html');
    }
}