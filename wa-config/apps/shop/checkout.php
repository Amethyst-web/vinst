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
      'vy' => 
      array (
        'required' => '1',
      ),
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
      'company' => 
      array (
        'localized_names' => 'Компания',
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
            'required' => '1',
          ),
          'city' => 
          array (
            'localized_names' => 'Город',
            'required' => '1',
          ),
          'country' => 
          array (
            'localized_names' => 'Страна',
            'required' => '1',
          ),
        ),
      ),
      'address.shipping' => 
      array (
        'localized_names' => 'Адрес доставки',
        'fields' => 
        array (
          'street' => 
          array (
            'localized_names' => 'Улица, дом, квартира',
            'required' => '1',
          ),
          'city' => 
          array (
            'localized_names' => 'Город',
            'required' => '1',
          ),
          'country' => 
          array (
            'localized_names' => 'Страна',
            'required' => '1',
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
