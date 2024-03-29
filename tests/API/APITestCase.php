<?php

namespace Tests\API;

use Gameap\Models\DedicatedServer;
use Gameap\Models\Game;
use Gameap\Models\GameMod;
use Gameap\Models\Server;
use Illuminate\Container\Container;
use Tests\TestCase;

class APITestCase extends TestCase
{
    /** @var Container */
    protected $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = Container::getInstance();
    }
}
