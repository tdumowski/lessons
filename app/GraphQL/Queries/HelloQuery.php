<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

class HelloQuery {
    public function __invoke($_, array $args) {
        return 'Witaj z Laravel 12 i GraphQL!';
    }
}