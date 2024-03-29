<?php

namespace Gameap\Http\Controllers\GdaemonAPI;

use Gameap\Http\Requests\GdaemonAPI\JsonServerBulkRequest;
use Gameap\Http\Requests\GdaemonAPI\ServerRequest;
use Gameap\Http\Responses\GdaemonAPI\ServerResponse;
use Gameap\Models\DedicatedServer;
use Gameap\Models\Server;
use Gameap\Repositories\ServerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

class ServersController extends Controller
{
    /**
     * @var ServerRepository
     */
    protected $repository;

    public function __construct(ServerRepository $serverRepository)
    {
        parent::__construct();

        $this->repository = $serverRepository;
    }

    public function index(DedicatedServer $dedicatedServer): JsonResponse
    {
        $servers = QueryBuilder::for(Server::where('ds_id', '=', $dedicatedServer->id))
        ->allowedFilters('id')
        ->with('dedicatedServer')
        ->with('game')
        ->with('gameMod')
        ->with('settings')
        ->get();

        $serversResponse = [];

        foreach ($servers as $server) {
            $serversResponse[] = new ServerResponse($server);
        }

        return response()->json($serversResponse);
    }

    public function server(Server $server): JsonResponse
    {
        return response()->json(new ServerResponse($server));
    }

    public function update(ServerRequest $request, Server $server): JsonResponse
    {
        $server->installed = $request->installed() ?? $server->installed;
        $server->process_active = $request->processActive() ?? $server->process_active;
        $server->last_process_check = $request->lastProcessCheck() ?? $server->last_process_check;

        $this->repository->save($server);

        return response()->json(['message' => 'success'], Response::HTTP_OK);
    }

    public function updateBulk(JsonServerBulkRequest $request): JsonResponse
    {
        $this->repository->saveBatch($request->values());

        return response()->json(['message' => 'success'], Response::HTTP_OK);
    }
}
