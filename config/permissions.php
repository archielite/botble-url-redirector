<?php

return [
    [
        'name' => 'URL',
        'flag' => 'url-redirector.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'url-redirector.create',
        'parent_flag' => 'url-redirector.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'url-redirector.edit',
        'parent_flag' => 'url-redirector.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'url-redirector.destroy',
        'parent_flag' => 'url-redirector.index',
    ],
];
