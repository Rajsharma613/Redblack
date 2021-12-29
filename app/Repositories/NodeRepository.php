<?php

namespace Gameap\Repositories;

use Gameap\Models\DedicatedServer;
use Gameap\Services\Daemon\CertificateService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NodeRepository extends Repository
{
    /**
     * @var \Gameap\Repositories\ClientCertificateRepository
     */
    protected $clientCertificateRepository;

    /**
     * @param DedicatedServer $dedicatedServer
     * @param \Gameap\Repositories\ClientCertificateRepository $clientCertificateRepository
     */
    public function __construct(
        DedicatedServer $dedicatedServer,
        ClientCertificateRepository $clientCertificateRepository
    ) {
        $this->model                       = $dedicatedServer;
        $this->clientCertificateRepository = $clientCertificateRepository;
    }

    public function find()
    {
        return DedicatedServer::all();
    }

    public function findById(int $id): ?DedicatedServer
    {
        return DedicatedServer::findOrFail($id);
    }

    /**
     * @param int $perPage
     * @return mixed
     */
    public function getAll($perPage = 20)
    {
        return DedicatedServer::orderBy('id')->withCount('servers')->paginate($perPage);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getIpList(int $id)
    {
        return $this->model->select('ip')
            ->where('id', '=', $id)
            ->first()
            ->ip;
    }

    /**
     * Get all busy ports for dedicated servers. Group by ip
     *
     * @param int $id
     * @return \Illuminate\Support\Collection|array
     */
    public function getBusyPorts(int $id)
    {
        /** @var DedicatedServer $dedicatedServer */
        $dedicatedServer = $this->model->select('id')->where('id', '=', $id)->first();
        $result          = [];

        foreach ($dedicatedServer->servers as $server) {
            if (!array_key_exists($server->server_ip, $result)) {
                $result[$server->server_ip] = [];
            }

            array_push($result[$server->server_ip], $server->server_port);
            array_push($result[$server->server_ip], $server->query_port);
            array_push($result[$server->server_ip], $server->rcon_port);
        }

        // Unique
        foreach ($result as &$ipList) {
            $ipList = array_values(array_unique($ipList, SORT_NUMERIC));
        }

        return $result;
    }

    /**
     * @param array $attributes
     * @return DedicatedServer
     */
    public function store(array $attributes)
    {
        $attributes['ip'] = array_filter($attributes['ip'], function ($value) {
            return !empty($value);
        });

        if (empty($attributes['client_certificate_id'])) {
            $clientCertificate                   = $this->clientCertificateRepository->getFirstOrGenerate();
            $attributes['client_certificate_id'] = $clientCertificate->id;
        }

        $attributes['gdaemon_api_key'] = Str::random(64);

        $attributes['enabled'] = $attributes['enabled'] ?? 1;
        $attributes['os']      = $attributes['os'] ?? 'linux';

        return DedicatedServer::create($attributes);
    }

    /**
     * @param DedicatedServer $dedicatedServer
     * @throws \Exception
     */
    public function destroy(DedicatedServer $dedicatedServer): void
    {
        if ($dedicatedServer->gdaemon_server_cert != CertificateService::ROOT_CA_CERT &&
            Storage::disk('local')->exists($dedicatedServer->gdaemon_server_cert)
        ) {
            // TODO: Not working =(
            // Storage::disk('local')->delete($dedicatedServer->gdaemon_server_cert);

            $certificateFile = Storage::disk('local')
                ->getDriver()
                ->getAdapter()
                ->applyPathPrefix($dedicatedServer->gdaemon_server_cert);

            if (file_exists($certificateFile)) {
                unlink($certificateFile);
            }
        }

        $dedicatedServer->delete();
    }

    /**
     * @param array $fields
     * @param DedicatedServer        $dedicatedServer
     */
    public function update(DedicatedServer $dedicatedServer, array $attributes): void
    {
        $attributes['ip'] = array_filter($attributes['ip'], function ($value) {
            return !empty($value);
        });

        $dedicatedServer->update($attributes);
    }
}
