<?php

class shopProductbrandsPluginBackendEditAction extends waViewAction
{
    public function execute()
    {
        $brands_model = new shopProductbrandsModel();
        $brand = $brands_model->getBrand(waRequest::get('id'));
        $this->view->assign('brand', $brand);

        $filter = $brand['filter'] !== null ? explode(',', $brand['filter']) : null;
        $feature_filter = array();
        $feature_model = new shopFeatureModel();
        $features['price'] = array(
            'id' => 'price',
            'name' => 'Price'
        );
        $features += $feature_model->getFeatures('selectable', 1);
        $features += $feature_model->getFeatures('type', 'boolean');
        if (!empty($filter)) {
            foreach ($filter as $feature_id) {
                $feature_id = trim($feature_id);
                if (isset($features[$feature_id])) {
                    $feature_filter[$feature_id] = $features[$feature_id];
                    $feature_filter[$feature_id]['checked'] = true;
                    unset($features[$feature_id]);
                }
            }
        }
        $this->view->assign('filter_features', $feature_filter + $features);
    }
}