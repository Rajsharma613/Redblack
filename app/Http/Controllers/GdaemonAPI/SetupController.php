<?php

namespace Gameap\Http\Controllers\GdaemonAPI;

use Gameap\Exceptions\GameapException;
use Gameap\Repositories\DedicatedServersRepository;
use Gameap\Services\CertificateService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SetupController extends BaseController
{
    private const CREATE_TOKEN_LENGTH = 24;

    private const CREATE_TOKEN_TTL_IN_SECONDS = 3600;

    /**
     * The DedicatedServersRepository instance.
     *
     * @var \Gameap\Repositories\DedicatedServersRepository
     */
    public $repository;

    /**
     * DedicatedServersController constructor.
     * @param DedicatedServersRepository $repository
     */
    public function __construct(DedicatedServersRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Return path to daemon script setup
     */
    public function setup(string $token)
    {
        if (app()->has('debugbar')) {
            app('debugbar')->disable();
        }

        Cache::forget('gdaemonAutoSetupToken');

        $gdaemonCreateToken = Str::random(self::CREATE_TOKEN_LENGTH);
        Cache::put('gdaemonAutoCreateToken', $gdaemonCreateToken, self::CREATE_TOKEN_TTL_IN_SECONDS);

        return "export createToken={$gdaemonCreateToken};
            export panelHost=" . url('/') . ';
            curl -sL https://raw.githubusercontent.com/gameap/auto-install-scripts/master/install-gdaemon.sh | bash --';
    }

    /**
     * Creating a new Dedicated server. Uploading certificate
     *
     * Gets dedicated server attributes and server certificate
     * Return signed client certificate and signed server certificate
     *
     * @param string $token
     * @param Request $request
     * @return string
     *
     * @throws GameapException
     * @throws FileNotFoundException
     */
    public function create(string $token, Request $request)
    {
        if (app()->has('debugbar')) {
            app('debugbar')->disable();
        }
        
        $attributes = $request->all();

        if ($request->hasFile('gdaemon_server_cert')) {
            $csr                     = $request->file('gdaemon_server_cert')->get();
            $serverSignedCertificate = CertificateService::signCsr($csr);
        } else {
            return 'Error Empty GDdaemon server certificate';
        }
        
        $attributes['gdaemon_server_cert'] = CertificateService::ROOT_CA_CERT;
        
        $dedicatedServer = $this->repository->store($attributes);
        $certificate     = Storage::get(CertificateService::ROOT_CA_CERT);
        
        Cache::forget('gdaemonCreateToken');
        
        return "Success {$dedicatedServer->id} {$dedicatedServer->gdaemon_api_key}\n{$certificate}\n\n{$serverSignedCertificate}";
    }
}
