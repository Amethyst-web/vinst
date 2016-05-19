<?php
return array (
  'shipping' => 
  array (
    'name' => 'Доставка',
    'prompt_type' => '0',
  ),
  'contactinfo' => 
  array (
    'name' => 'Контактная информация',
    'fields' => 
    array (
      'firstname' => 
      array (
        'localized_names' => 'Имя',
        'required' => '',
      ),
      'lastname' => 
      array (
        'localized_names' => 'Фамилия',
        'required' => '',
      ),
      'phone' => 
      array (
        'localized_names' => 'Телефон',
        'required' => '1',
      ),
      'email' => 
      array (
        'localized_names' => 'Email',
        'required' => '1',
      ),
      'company' => 
      array (
        'localized_names' => 'Компания',
        'required' => '',
      ),
      'vy' => 
      array (
        'required' => '',
      ),
      'address' => 
      array (
        'localized_names' => 'Адрес',
        'fields' => 
        array (
          'street' => 
          array (
            'localized_names' => 'Улица, дом, квартира',
            'required' => '',
          ),
          'city' => 
          array (
            'localized_names' => 'Город',
            'required' => '',
          ),
          'region' => 
          array (
            'localized_names' => 'Регион',
            'required' => '',
          ),
          'zip' => 
          array (
            'localized_names' => 'Индекс',
            'required' => '',
          ),
          'country' => 
          array (
            'localized_names' => 'Страна',
            'required' => '',
          ),
        ),
      ),
      'address.shipping' => 
      array (
        'localized_names' => 'Адрес',
        'fields' => 
        array (
          'street' => 
          array (
            'localized_names' => 'Улица, дом, квартира',
            'required' => '',
          ),
          'city' => 
          array (
            'localized_names' => 'Город',
            'required' => '',
          ),
          'region' => 
          array (
            'localized_names' => 'Регион',
            'required' => '',
          ),
          'zip' => 
          array (
            'localized_names' => 'Индекс',
            'required' => '',
          ),
          'country' => 
          array (
            'localized_names' => 'Страна',
            'required' => '',
          ),
        ),
      ),
    ),
  ),
  'payment' => 
  array (
    'name' => 'Оплата',
  ),
  'confirmation' => true,
);
