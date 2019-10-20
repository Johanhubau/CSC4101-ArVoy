<?php

// config/routes.php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('owner_index', '/blog')
        // the controller value has the format [controller_class, method_name]
        ->controller([BlogController::class, 'list'])

        // if the action is implemented as the __invoke() method of the
        // controller class, you can skip the ', method_name]' part:
        // ->controller([BlogController::class])
    ;
};
