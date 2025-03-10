<?php

return [ 
    'client_id' => env('PAYPAL_CLIENT_ID','Adjzj0DBEkqwSIx1_at-dq7o4G3OavLrg9cx4ECHV_YZ1FsUZjGmIJHGoRc8Vi_hW2ZyWfs_HrE3VgoN'),
    'secret' => env('PAYPAL_SECRET','ECmiRcWVaavEZk3mxsPo6qHgPEUfjFF3I_8B7rt-ttw6GberrSkW83TnKbgmib2gI4cGUevFg5eP0f0E'),
    'settings' => array(
        'mode' => env('PAYPAL_MODE','sandbox'),
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paypal.log',
        'log.LogLevel' => 'ERROR'
    ),
];