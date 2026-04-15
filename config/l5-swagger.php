<?php

$config = require base_path('vendor/darkaonline/l5-swagger/config/l5-swagger.php');

$config['documentations']['default']['api']['title'] = 'Bhojnalaya API Documentation';
$config['defaults']['generate_always'] = env('L5_SWAGGER_GENERATE_ALWAYS', true);
$config['defaults']['paths']['docs'] = storage_path('api-docs');
$config['documentations']['default']['paths']['annotations'] = [
    base_path('app'),
];
$config['defaults']['scanOptions']['analyser'] = new \OpenApi\Analysers\ReflectionAnalyser([
    new \OpenApi\Analysers\AttributeAnnotationFactory(),
    new \OpenApi\Analysers\DocBlockAnnotationFactory(),
]);

return $config;
