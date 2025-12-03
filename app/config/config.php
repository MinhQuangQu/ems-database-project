<?php

return [

    'base_url' => '/CSDL/public', // đường dẫn đến thư mục public
    'debug'    => true,

    'environment' => 'local',

    'app_name'      => 'EMS - Employee Management System',
    'app_version'   => '1.0.0', 

    'timezone'      => 'Asia/Ho_Chi_Minh',
    'locale'        => 'vi',

    // Format ngày–giờ
    'datetime_format' => 'Y-m-d H:i:s',
    'date_format'     => 'Y-m-d',
    'time_format'     => 'H:i:s',

    'session_name' => 'ems_session',
    'csrf_token_key' => 'ems_csrf',

    // SALT riêng để tăng bảo mật
    'app_salt' => 'x82asj182nasd182ASD9182asd__EMS',

    // Thời gian session (30 phút)
    'session_lifetime' => 1800, 

    'pagination' => [
        'per_page' => 15
    ],

    'log_path'       => __DIR__ . '/../../storage/logs/',
    'log_errors'     => true,
    'log_sql'        => false,    // bật để debug query


    'uploads_path'    => __DIR__ . '/../../public/uploads/',
    'uploads_url'     => '/uploads/',

];

