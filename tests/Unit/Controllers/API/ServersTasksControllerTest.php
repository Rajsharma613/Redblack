<?php

namespace Tests\Unit\Controllers\API;

use Gameap\Http\Controllers\API\ServersTasksController;
use Gameap\Http\Requests\API\ServerTaskCreateRequest;
use Gameap\Http\Requests\API\ServerTaskUpdateRequest;
use Gameap\Models\Server;
use Gameap\Models\ServerTask;
use Gameap\Repositories\ServersTasksRepository;
use Illuminate\Http\JsonResponse;
use Silber\Bouncer\Bouncer;
use Tests\TestCase;
use Mockery;

/**
 * @covers \Gameap\Http\Controllers\API\ServersTasksController
 */
class ServersTasksControllerTest extends TestCase
{
    /** @var ServersTasksController */
    protected $controller;

    /** @var ServersTasksRepository */
    protected $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(ServersTasksRepository::class);
        $this->controller = $this->createPartialMock(ServersTasksController::class, ['authorize']);

        $this->controller->__construct($this->repositoryMock);

        $this->bouncer = $this->app->get(Bouncer::class);
        $this->bouncer->dontCache();
    }

    /**
     * @throws \Gameap\Exceptions\Repositories\RepositoryValidationException
     */
    public function testUpdate()
    {
        $this->controller->method('authorize')->willReturn(true);
        $this->repositoryMock->shouldReceive('update')->andReturnNull();

        $requestMock = Mockery::mock(ServerTaskUpdateRequest::class);
        $requestMock->shouldReceive('all')->andReturn([
            'command'       => 'start',
            'repeat'        => '0',
            'repeat_period' => '1 day',
            'execute_date'  => '2020-05-23 00:00:00',
        ]);

        $serverTask = new ServerTask();
        $serverTask->id = 1337;

        $result = $this->controller->update(
            $requestMock,
            Mockery::mock(Server::class),
            $serverTask
        );

        $this->assertEquals(['message' => 'success'], $result->getData(true));
    }

    public function testGetList()
    {
        $this->controller->method('authorize')->willReturn(true);
        $task = [
            'id'            => '1',
            'command'          => 'restart',
            'server_id'     => 1,
            'repeat'        => 0,
            'repeat_period' => '10 minutes',
            'counter'       => 1,
            'execute_date'  => '2020-03-18 22:11:00',
            'payload'       => null,
        ];

        $this->repositoryMock->shouldReceive('getTasks')->andReturn([$task]);
        $server = Mockery::mock(Server::class);
        $server->shouldReceive('getAttribute')->andReturn(1);
        $result = $this->controller->getList($server);

        $this->assertEquals([$task], $result);
    }

    /**
     * @throws \Gameap\Exceptions\Repositories\RepositoryValidationException
     */
    public function testStore()
    {
        $requestMock = Mockery::mock(ServerTaskCreateRequest::class);
        $requestMock->shouldReceive('all')->andReturn([
            'server_id'     => Server::all()->random()->id,
            'command'       => 'start',
            'repeat'        => '0',
            'repeat_period' => '1 day',
            'execute_date'  => '2020-05-23 00:00:00',
        ]);

        $this->repositoryMock->shouldReceive('store')->andReturn(1337);

        $result = $this->controller->store($requestMock);

        $this->assertEquals(
            [
                'message' => 'success',
                'serverTaskId' => 1337
            ],
            $result->getData(true));

        $this->assertEquals(JsonResponse::HTTP_CREATED, $result->getStatusCode());
    }

    public function testDestroy()
    {
        $serverTask = Mockery::mock(ServerTask::class);
        $serverTask->shouldReceive('delete')->andReturnNull();

        $result = $this->controller->destroy(
            Mockery::mock(Server::class),
            $serverTask
        );

        $this->assertEquals(['message' => 'success'], $result->getData(true));
    }
}
