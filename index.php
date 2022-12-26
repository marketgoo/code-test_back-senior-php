<?php
/**
 * Prueba de código para MarketGoo. ¡Lee el README.md!
 */
require __DIR__."/vendor/autoload.php";
require_once('helpers/GeoLocator.php');

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response as Response;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Slim\Factory\AppFactory;
use GraphQL\Error\DebugFlag;
use Helpers\GeoLocator as GeoLocator;

// Datos estáticos que modelan los resultados de la consulta GraphQL
$users = [
    1 => ["id" => 1, "name" => "Sergio Palma", "ip" => "188.223.227.125", "ip_region" => GeoLocator::getLocation("188.223.227.125")],
    2 => ["id" => 2, "name" => "Manolo Engracia", "ip" => "194.191.232.168", "ip_region" => GeoLocator::getLocation("194.191.232.168")],
    3 => ["id" => 3, "name" => "Fito Cabrales", "ip" => "77.162.109.160", "ip_region" => GeoLocator::getLocation("77.162.109.160")]
];

// Definimos el schema del tipo de dato "Usuario" para GraphQL
$graphql_user_type = new ObjectType([
    "name" => "User",
    "fields" => [
        "id" => Type::int(),
        "name" => Type::string(),
        "ip" => Type::string(),
        "ip_region" => Type::string()
    ]
]);

// Instanciamos la aplicación Slim. Es tan sencilla que sólo vamos a usar aquí
// la ruta "/graphql" para este test. Todo lo demás es por defecto.
$app = AppFactory::create();
$app->map(["GET", "POST"], "/graphql", function(Request $request, Response $response) {
    global $users, $graphql_user_type;
    $debug = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
    try {
        $graphQLServer = new \GraphQL\Server\StandardServer([
            "schema" => new Schema([
                "query" => new ObjectType([
                    "name" => "Query",
                    "fields" => [
                        "user" => [
                            "type" => $graphql_user_type,
                            "args" => [
                                "id" => Type::nonNull(Type::int())
                            ],
                            "resolve" => function ($rootValue, $args) use ($users) {
                                return $users[$args["id"]] ?? $users[$args["id"]];
                            }
                        ],
                        "users" => [
                            "type" => Type::listOf($graphql_user_type),
                            "resolve" => function() use ($users) {
                                return $users;
                            }
                        ]
                    ]
                ])
            ]),
            "debugFlag" => $debug
        ]);

        return $graphQLServer->processPsrRequest($request, $response, $response->getBody());
    } catch (\Exception $e) {
        return $response->withStatus($e?->getCode() ?: 500)->withJson([
            "errors" => [\GraphQL\Error\FormattedError::createFromException($e, $debug)]
        ]);
    }
});

$app->run();
