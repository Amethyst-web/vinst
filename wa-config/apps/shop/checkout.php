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
          'transportnaya-k' => 
          array (
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
