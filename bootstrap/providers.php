<?php

return [
    App\Providers\AppServiceProvider::class,

    // Pivor Modules
    App\Modules\Core\Providers\CoreServiceProvider::class,
    App\Modules\Clients\Providers\ClientsServiceProvider::class,
    App\Modules\Contacts\Providers\ContactsServiceProvider::class,
    App\Modules\Communications\Providers\CommunicationsServiceProvider::class,
];
