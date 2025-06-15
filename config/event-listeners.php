<?php
declare(strict_types=1);

/**
 * @var array<class-string, class-string[]>
 */
return [
    \App\Events\TestEvent::class => [
        \App\Listeners\TestListener::class,
    ]
];