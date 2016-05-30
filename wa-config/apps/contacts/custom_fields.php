<?php
return array (
  0 => 
  waContactBranchField::__set_state(array(
     'validate_range' => true,
     'id' => 'vy',
     'options' => 
    array (
      'app_id' => 'shop',
      'storage' => 'data',
      'options' => 
      array (
        'Физическое лицо' => 'Физическое лицо',
        'Юридическое лицо' => 'Юридическое лицо',
      ),
      'hide' => 
      array (
        'Физическое лицо' => 
        array (
          0 => 'company',
        ),
        'Юридическое лицо' => 
        array (
          0 => 'firstname',
          1 => 'lastname',
        ),
      ),
      'required' => false,
      'allow_self_edit' => false,
      'unique' => false,
    ),
     'name' => 
    array (
      'en_US' => 'Вы',
    ),
     '_type' => 'waContactBranchField',
  )),
  1 => 
  waContactAddressField::__set_state(array(
     'id' => 'address',
     'options' => 
    array (
      'multi' => true,
      'ext' => 
      array (
        'work' => 'work',
        'home' => 'home',
        'shipping' => 'shipping',
        'billing' => 'billing',
      ),
      'storage' => 'data',
      'fields' => 
      array (
        'street' => 
        waContactStringField::__set_state(array(
           'id' => 'street',
           'options' => 
          array (
            'storage' => 'data',
            'validators' => 
            waStringValidator::__set_state(array(
               'messages' => 
              array (
                'required' => 'Нужно заполнить',
                'invalid' => 'Неверно',
                'max_length' => 'Пожалуйста, не более 0 символов',
                'min_length' => 'Пожалуйста, не менее 0 символов',
              ),
               'options' => 
              array (
                'required' => false,
                'storage' => 'data',
              ),
               'errors' => 
              array (
              ),
               '_type' => 'waStringValidator',
            )),
          ),
           'name' => 
          array (
            'en_US' => 'Street address',
          ),
           '_type' => 'waContactStringField',
        )),
        'city' => 
        waContactStringField::__set_state(array(
           'id' => 'city',
           'options' => 
          array (
            'storage' => 'data',
            'validators' => 
            waStringValidator::__set_state(array(
               'messages' => 
              array (
                'required' => 'Нужно заполнить',
                'invalid' => 'Неверно',
                'max_length' => 'Пожалуйста, не более 0 символов',
                'min_length' => 'Пожалуйста, не менее 0 символов',
              ),
               'options' => 
              array (
                'required' => false,
                'storage' => 'data',
              ),
               'errors' => 
              array (
              ),
               '_type' => 'waStringValidator',
            )),
          ),
           'name' => 
          array (
            'en_US' => 'City',
          ),
           '_type' => 'waContactStringField',
        )),
        'region' => 
        waContactRegionField::__set_state(array(
           'rm' => NULL,
           'id' => 'region',
           'options' => 
          array (
            'storage' => 'data',
          ),
           'name' => 
          array (
            'en_US' => 'State',
          ),
           '_type' => 'waContactRegionField',
        )),
        'zip' => 
        waContactStringField::__set_state(array(
           'id' => 'zip',
           'options' => 
          array (
            'storage' => 'data',
            'validators' => 
            waStringValidator::__set_state(array(
               'messages' => 
              array (
                'required' => 'Нужно заполнить',
                'invalid' => 'Неверно',
                'max_length' => 'Пожалуйста, не более 0 символов',
                'min_length' => 'Пожалуйста, не менее 0 символов',
              ),
               'options' => 
              array (
                'required' => false,
                'storage' => 'data',
              ),
               'errors' => 
              array (
              ),
               '_type' => 'waStringValidator',
            )),
          ),
           'name' => 
          array (
            'en_US' => 'ZIP',
          ),
           '_type' => 'waContactStringField',
        )),
        'country' => 
        waContactCountryField::__set_state(array(
           'model' => NULL,
           'validate_range' => true,
           'id' => 'country',
           'options' => 
          array (
            'defaultOption' => 'Select country',
            'storage' => 'data',
            'formats' => 
            array (
              'value' => 
              waContactCountryFormatter::__set_state(array(
                 '_type' => 'waContactCountryFormatter',
                 'options' => NULL,
              )),
            ),
          ),
           'name' => 
          array (
            'en_US' => 'Country',
          ),
           '_type' => 'waContactCountryField',
        )),
        'transportnaya-k' => 
        waContactStringField::__set_state(array(
           'id' => 'transportnaya-k',
           'options' => 
          array (
            'app_id' => 'shop',
            'storage' => 'data',
            'validators' => 
            waStringValidator::__set_state(array(
               'messages' => 
              array (
                'required' => 'Нужно заполнить',
                'invalid' => 'Неверно',
                'max_length' => 'Пожалуйста, не более 0 символов',
                'min_length' => 'Пожалуйста, не менее 0 символов',
              ),
               'options' => 
              array (
                'required' => false,
                'app_id' => 'shop',
                'storage' => 'data',
              ),
               'errors' => 
              array (
              ),
               '_type' => 'waStringValidator',
            )),
            'required' => '1',
          ),
           'name' => 
          array (
            'en_US' => 'Транспортная компания',
          ),
           '_type' => 'waContactStringField',
        )),
        'lng' => 
        waContactHiddenField::__set_state(array(
           'id' => 'lng',
           'options' => 
          array (
            'storage' => 'data',
          ),
           'name' => 
          array (
            'en_US' => 'Longitude',
          ),
           '_type' => 'waContactHiddenField',
        )),
        'lat' => 
        waContactHiddenField::__set_state(array(
           'id' => 'lat',
           'options' => 
          array (
            'storage' => 'data',
          ),
           'name' => 
          array (
            'en_US' => 'Latitude',
          ),
           '_type' => 'waContactHiddenField',
        )),
      ),
      'formats' => 
      array (
        'js' => 
        waContactAddressOneLineFormatter::__set_state(array(
           '_type' => 'waContactAddressOneLineFormatter',
           'options' => NULL,
        )),
        'forMap' => 
        waContactAddressForMapFormatter::__set_state(array(
           '_type' => 'waContactAddressForMapFormatter',
           'options' => NULL,
        )),
      ),
      'required' => 
      array (
      ),
      'allow_self_edit' => false,
      'unique' => false,
    ),
     'name' => 
    array (
      'en_US' => 'Address',
    ),
     '_type' => 'waContactAddressField',
  )),
);
