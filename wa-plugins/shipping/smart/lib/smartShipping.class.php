<?php

/**
 * @author fullpro.ru <hello@fullpro.ru>
 * @link http://fullpro.ru/
 *
 * @property-read array $rate_zone
 * @property-read string $currency
 * @property-read string $weight_dimension
 * @property-read string $delivery_time
 * @property-read int $currency_round
 * @property-read array $map
 * @property-read array $zones
 *
 */
class smartShipping extends waShipping
{

    const
        CALLBACKS_FILE_NAME = 'smartShippingCallbacks.js',
        CUSTOMCSS_FILE_NAME = 'smartShippingCustomStyle.css';


    /**
     * Example of direct usage HTML templates instead waHtmlControl
     * (non-PHPdoc)
     * @see waShipping::getSettingsHTML()
     */
    public function getSettingsHTML($params = array())
    {

        $values = $this->getSettings();
        
        if (!empty($params['value'])) {
            $values = array_merge($values, $params['value']);
        }

        if (!isset($values['map']['show_map'])) {
            $values['map']['show_map'] = 1; // показывать карту для пользователей старой версии
        }
        
        if (!$values['rate_zone']['country']) {
            $l = substr(wa()->getUser()->getLocale(), 0, 2);
            if ($l == 'ru') {
                $values['rate_zone']['country'] = 'rus';
                $values['rate_zone']['region'] = '77';
                $values['city'] = '';
            } else {
                $values['rate_zone']['country'] = 'usa';
            }
        }

        $view = wa()->getView();

        $cm = new waCountryModel();
        $view->assign('countires', $cm->all());

        if (!empty($values['rate_zone']['country'])) {
            $rm = new waRegionModel();
            $view->assign('regions', $rm->getByCountry($values['rate_zone']['country']));
        }

        $app_config = wa()->getConfig();
        if (method_exists($app_config, 'getCurrencies')) {
            $view->assign('currencies', $app_config->getCurrencies());
        }

        $namespace = '';
        if (!empty($params['namespace'])) {
            if (is_array($params['namespace'])) {
                $namespace = array_shift($params['namespace']);
                while (($namspace_chunk = array_shift($params['namespace'])) !== null) {
                    $namespace .= "[{$namspace_chunk}]";
                }
            } else {
                $namespace = $params['namespace'];
            }
        }

        $zones = isset($values['zones']) ? $values['zones'] : array();
        $zonesSettings = array();
        foreach ($zones as $i => $zone) {
            $zones[$i]['rates'] = $this->array_orderby($zone['rates'], 'sum', SORT_ASC, 'distance', SORT_ASC, 'weight', SORT_ASC);
            $zonesSettings[] = $zones[$i];
        }

        $view->assign('ss_v', $this->getProperties('version'));
        $view->assign('namespace', $namespace);
        $view->assign('values', $values);
        $view->assign('p', $this);
        $view->assign('zones_settings', json_encode($zonesSettings));

        // callbacks
        $callbackFile = wa()->getDataPath(self::CALLBACKS_FILE_NAME, true, 'shop', true);
        $callbacks = file_get_contents($callbackFile);

        // Перенести старые колбэки в новый файл и удалить старый
        $oldCallbacksFile = realpath(__DIR__ . '/../js/zones-callbacks.js'); // to be removed in future
        if ((!$callbacks || $callbacks == '') && file_exists($oldCallbacksFile)) {
            $callbacks = file_get_contents($oldCallbacksFile);
            $this->updateCallbacksFile($callbacks);
            @unlink($oldCallbacksFile);
        }
        $view->assign('callbacks', $callbacks);


        // styles
        $stylesFile = wa()->getDataPath(self::CUSTOMCSS_FILE_NAME, true, 'shop', true);
        $styles = file_get_contents($stylesFile);
        $view->assign('styles', $styles);

        $html = '';

        $view->assign('xhr_url', wa()->getAppUrl('webasyst').'?module=backend&action=regions');

        $html .= parent::getSettingsHTML($params);

        $html .= $view->fetch($this->path.'/templates/settings.html');

        return $html;
    }

    function array_orderby() {
        $args = func_get_args();
        $data = array_shift( $args );

        if ( ! is_array( $data ) ) {
            return array();
        }

        $multisort_params = array();
        foreach ( $args as $n => $field ) {
            if ( is_string( $field ) ) {
                $tmp = array();
                foreach ( $data as $row ) {
                    $tmp[] = isset($row[ $field ]) ? $row[ $field ] : 0;
                }
                $args[ $n ] = $tmp;
            }
            $multisort_params[] = &$args[ $n ];
        }

        $multisort_params[] = &$data;
        call_user_func_array( 'array_multisort', $multisort_params );
        return end( $multisort_params );
    }

    public function calculate()
    {
        $cookie_key = 'smart_shipping_'.$this->key.'_distances';

        if ($distances = wa()->getRequest()->post('smart_shipping_'.$this->key.'_distance')) {

            switch ($distances) {
                // маршруты не найдены, потому что пустое поле
                case '_':
                    wa()->getResponse()->setCookie($cookie_key, $distances, time() - 3600*2);
                    return $this->_w('Specify address to calculate shipping cost');
                    break;

                // маршруты по заданному адресу не найдены
                case 'no_routes':
                    return $this->_w('No routes found to the specified address');
                    break;

                // превышен лимит длины максимального маршрута
                case 'max_route_distance_exceeded':
                    return $this->_w('We do not ship to the specified address.');//('An error occurred while finding the route to the specified address');
                    break;

                // адрес, куда доставка невозможна
                case 'no_delivery_here':
                    return $this->_w('We do not ship to the specified address.');
                    break;

                default:
                    wa()->getResponse()->setCookie($cookie_key, $distances, time() + 3600*2);
            }

        } else {
            $distances = waRequest::cookie($cookie_key, '{}');
        }

        $distances = @json_decode($distances, true);
        ///////////////

        $orderPrice = $this->getPackageProperty('price');
        $orderWeight = $this->getPackageProperty('weight');
        $price = 0;
        $text = '';

        if (is_array($distances) && is_array($this->zones)) {
            $zones = array();
            foreach ($this->zones as $zone) {
                $zoneId = isset($zone['id']) && $zone['id'] != '' ? $zone['id'] : self::transliterate($zone['title'], true);
                $zones[$zoneId] = $zone;
            }

            $i = 0;
            foreach ($distances as $zoneId => $distance) {

                if (!isset($distance['in']) && !isset($distance['boundary'])) {
                    continue;
                } elseif (!isset($distance['boundary'])) {
                    $distance['boundary'] = 0;
                } elseif (!isset($distance['in'])) {
                    $distance['in'] = 0;
                }

                $totalDistance = round($distance['in'] + $distance['boundary']);

                if (!isset($zones[$zoneId]) || !isset($zones[$zoneId]['rates'])) {
                    continue;
                }

                $rates = $this->array_orderby($zones[$zoneId]['rates'], 'sum', SORT_DESC, 'distance', SORT_DESC, 'weight', SORT_DESC);

                foreach ($rates as $rate) {
                    $rate = array_map('floatval', $rate);

                    if ($rate['sum'] <= $orderPrice && $rate['distance'] <= $totalDistance && $rate['weight'] <= $orderWeight && $totalDistance > 0) {
                        $zonePrice = 0;
                        $zoneText = $zones[$zoneId]['title'].': '.$totalDistance.'км, ';

                        switch($rate['type']) {
                            // фикс
                            case 1:
                                $zonePrice = $rate['cost_fix'];
                                $zoneText .= $zonePrice . ' '. $this->currency;
                                break;
                            // дистанция
                            case 2:
                                $zonePrice = $rate['cost_distance'] * $totalDistance;
                                $zoneText .= $rate['cost_distance']. $this->currency.'/км='.$zonePrice . ' '. $this->currency;
                                break;
                            // фикс + дистанция
                            case 3:
                                $zonePrice = $rate['cost_fix'] + $rate['cost_distance'] * $totalDistance;
                                $zoneText .= $rate['cost_fix'].'+'.$rate['cost_distance']. ' '. $this->currency.'/'.'км='.$zonePrice. ' '. $this->currency;
                                break;
                            // вес
                            case 4:
                                $zonePrice = $rate['cost_weight'] * $orderWeight;
                                $zoneText .= $rate['cost_weight']. $this->currency.'/кг='.$zonePrice . ' '. $this->currency;
                                break;
                            // вес + фикс
                            case 5:
                                $zonePrice = ($rate['cost_weight'] * $orderWeight) + $rate['cost_fix'];
                                $zoneText .= $rate['cost_fix'].'+'.$rate['cost_weight']. $this->currency.'/кг='.$zonePrice . ' '. $this->currency;
                                break;
                            // вес + дистанция
                            case 6:
                                $zonePrice = ($rate['cost_weight'] * $orderWeight) + $rate['cost_distance'] * $totalDistance;
                                $zoneText .= $rate['cost_weight']. $this->currency.'/кг+'.$rate['cost_distance']. $this->currency.'/км='.$zonePrice . ' '. $this->currency;
                                break;
                            // вес + фикс + дистанция
                            case 7:
                                $zonePrice = $rate['cost_fix'] + ($rate['cost_weight'] * $orderWeight) + ($rate['cost_distance'] * $totalDistance);
                                $zoneText .= $rate['cost_fix'] . ' '. $this->currency . '+' . $rate['cost_weight']. $this->currency.'/кг+'.$rate['cost_distance']. $this->currency.'/км='.$zonePrice . ' '. $this->currency;
                                break;
                        }

                        $price += $zonePrice;
                        $text .= $zoneText . "<br>";
                        break;
                    }
                }
            }
        }

        // округлить стоимость доставки
        if ($this->currency_round) {
            $price = round($price / $this->currency_round) * $this->currency_round;
        }

        if ($this->delivery_time) {
            $delivery_date = array_map('strtotime', explode(',', $this->delivery_time, 2));
            foreach ($delivery_date as & $date) {
                $date = waDateTime::format('humandate', $date);
            }
            unset($date);
            $delivery_date = implode(' —', $delivery_date);
        } else {
            $delivery_date = null;
        }

        return array(
            'delivery' => array(
                'est_delivery' => $delivery_date,
                'currency'     => $this->currency,
                'rate'         => $price,
                'text'         => $text
            ),
        );
    }

    public function allowedAddress()
    {
        $rate_zone = $this->rate_zone;
        $address = array();
        foreach ($rate_zone as $field => $value) {
            if (!empty($value)) {
                $address[$field] = $value;
            }
        }
        return array($address);
    }

    public function allowedCurrency()
    {
        return $this->currency;
    }

    public function allowedWeightUnit()
    {
        return $this->weight_dimension;
    }

    public function requestedAddressFields()
    {
        return array(
            'city'                  => array('cost' => true),
            'street'                => array(),
            'smart_shipping_data'   => array('hidden' => true, 'value' => ''),
        );
    }

    public function getPrintForms(waOrder $order = null)
    {
        return array(
            'delivery_list' => array(
                'name'        => _wd('shipping_smart', 'Packing list'),
                'description' => _wd('shipping_smart', 'Order summary for smart shipping'),
            ),
        );
    }

    public function displayPrintForm($id, waOrder $order, $params = array())
    {
        if ($id = 'delivery_list') {
            $view = wa()->getView();
            $main_contact_info = array();
            foreach (array('email', 'phone', ) as $f) {
                if (($v = $order->contact->get($f, 'top,html'))) {
                    $main_contact_info[] = array(
                        'id'    => $f,
                        'name'  => waContactFields::get($f)->getName(),
                        'value' => is_array($v) ? implode(', ', $v) : $v,
                    );
                }
            }

            $formatter = new waContactAddressSeveralLinesFormatter();
            $shipping_address = array();
            foreach (waContactFields::get('address')->getFields() as $k => $v) {
                if (isset($order->params['shipping_address.'.$k])) {
                    $shipping_address[$k] = $order->params['shipping_address.'.$k];
                }
            }

            $shipping_address_text = array();
            foreach (array('country_name', 'region_name', 'zip', 'city', 'street') as $k) {
                if (isset($order->shipping_address[$k])) {
                    $shipping_address_text[] = $order->shipping_address[$k];
                }
            }
            $shipping_address_text = implode(', ', $shipping_address_text);
            $view->assign('shipping_address_text', $shipping_address_text);
            $shipping_address = $formatter->format(array('data' => $shipping_address));
            $shipping_address = $shipping_address['value'];

            $view->assign('shipping_address', $shipping_address);
            $view->assign('main_contact_info', $main_contact_info);
            $view->assign('order', $order);
            $view->assign('params', $params);
            $view->assign('p', $this);
            return $view->fetch($this->path.'/templates/form.html');
        } else {
            throw new waException('Print form not found');
        }
    }

    public function customFields(waOrder $order)
    {

        return array(
            'smart_shipping_data' => array(
            'control_type'      => waHtmlControl::CUSTOM,
            'title'             => $this->_w('Shipping route'),
            'callback'          => array($this, 'smartShippingWidget')
            ),
            'incorrect' => array(
                'control_type'  => waHtmlControl::CHECKBOX,
                'value'         => $this->_w('yes'),
                'checked'       => false,
                'title'         => $this->_w('Address on map was determined incorrectly'),
                'description'   => $this->_w('Check if destination point on map was determined incorrectly'),
            )
        );
    }

    public function smartShippingWidget() {

        $pluginPath = waConfig::get('wa_path_plugins') . '/shipping/smart/';
        $pluginPublicPath = wa()->getRootUrl() . str_replace(waConfig::get('wa_path_root') . DIRECTORY_SEPARATOR, '', $pluginPath);

        $view = wa()->getView();
        $view->assign('public_path', $pluginPublicPath);
        $view->assign('shipping_plugin_id', $this->key);
        $view->assign('calculate_button_title', $this->_w('Calculate cost of shipping'));
        $view->assign('callbacks_file_url', wa()->getDataUrl(self::CALLBACKS_FILE_NAME, true));
        $view->assign('customcss_file_url', wa()->getDataUrl(self::CUSTOMCSS_FILE_NAME, true));

        $settings = $this->getSettings(); $keepSettings = array('map', 'zones'); $settingsToAssign = array();

        foreach($settings as $settingName => $setting) {
            if (in_array($settingName, $keepSettings)) {

                switch($settingName) {
                    case 'zones':
                        $zones = $setting; $setting = array();
                        foreach ($zones as $zone) {
                            if (isset($zone['enabled']) && $zone['enabled'] == 1) {

                                if ($zone['coordinates'] == '' || $zone['id'] == '_outside') {
                                    continue;
                                }

                                $coordinates = json_decode($zone['coordinates']);
                                $setting[] = array(
                                    'id'        => isset($zone['id']) && $zone['id'] != '' ? $zone['id'] : self::transliterate($zone['title'], true),
                                    'geometry'  => array('type' => 'Polygon', 'coordinates' => $coordinates->coordinates),
                                    'options'   => json_decode($zone['options'], true),
                                );
                            }

                        }
                        $setting = json_encode($setting);
                        break;
                    case 'map':
                        $setting['zoom'] = isset($setting['zoom']) ? min(23, intval($setting['zoom'])) : 9;
                        $setting['route_max_length'] = isset($setting['route_max_length']) ? min(500, intval($setting['route_max_length'])) : 500;
                        break;
                }

                $settingsToAssign[$settingName] = $setting;
            }
        }

        $view->assign('settings', $settingsToAssign);

        $out = $view->fetch($pluginPath . 'templates/widget.html');
        return $out;
    }

    public function saveSettings($settings = array()) {

        // обновить колбэки
        if (isset($settings['callbacks'])) {
            $callbacks = $settings['callbacks'];
            unset($settings['callbacks']);
        } else {
            $callbacks = '';
        }

        $this->updateCallbacksFile($callbacks);

        // обновить стили
        if (isset($settings['styles'])) {
            $styles = $settings['styles'];
            unset($settings['styles']);
        } else {
            $styles = '';
        }

        $this->updateStylesFile($styles);

        // показ карты
        if (!isset($settings['map']['show_map'])) {
            $settings['map']['show_map'] = 0;
        }

        parent::saveSettings($settings);

    }

    /**
     * Suggest id from title
     * @param string $str
     * @param boolean $strict
     * @return string
     */
    public static function transliterate($str, $strict = true)
    {
        $str = preg_replace('/\s+/u', '-', $str);
        if ($str) {
            foreach (waLocale::getAll() as $lang) {
                $str = waLocale::transliterate($str, $lang);
            }
        }
        $str = preg_replace('/[^a-zA-Z0-9_-]+/', '', $str);
        if ($strict && !strlen($str)) {
            $str = date('Ymd');
        }
        return strtolower($str);
    }

    protected function updateCallbacksFile($callbacks) {
        $callbackFile = wa()->getDataPath(self::CALLBACKS_FILE_NAME, true, 'shop', true);
        $file = fopen($callbackFile, 'w');
        if (!$file) {
            throw new waException('Не удаётся сохранить файл с колбэками. Проверьте права на запись ' . $callbackFile);
        }
        fwrite($file, $callbacks);
        fclose($file);
    }

    protected function updateStylesFile($styles) {
        $stylesFile = wa()->getDataPath(self::CUSTOMCSS_FILE_NAME, true, 'shop', true);
        $file = fopen($stylesFile, 'w');
        if (!$file) {
            throw new waException('Не удаётся сохранить файл со стилями. Проверьте права на запись ' . $stylesFile);
        }
        fwrite($file, $styles);
        fclose($file);
    }

}
