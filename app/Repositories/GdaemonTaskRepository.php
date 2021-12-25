<?php

namespace Gameap\Repositories;

use Gameap\Exceptions\Repositories\GdaemonTaskRepository\EmptyServerStartCommandException;
use Gameap\Exceptions\Repositories\GdaemonTaskRepository\GdaemonTaskRepositoryException;
use Gameap\Exceptions\Repositories\GdaemonTaskRepository\InvalidServerStartCommandException;
use Gameap\Exceptions\Repositories\RecordExistExceptions;
use Gameap\Models\GdaemonTask;
use Gameap\Models\Server;
use Illuminate\Support\Facades\DB;
use PDO;

class GdaemonTaskRepository extends Repository
{
    public function __construct(GdaemonTask $gdaemonTask)
    {
        $this->model = $gdaemonTask;
    }

    public function getAll($perPage = 20)
    {
        return GdaemonTask::orderBy('id', 'DESC')->paginate($perPage);
    }

    public function taskExists(Server $server, string $task, array $statuses): bool
    {
        $taskQuery = GdaemonTask::where([
            ['task', '=', $task],
            ['server_id', '=', $server->id],
            ['dedicated_server_id', '=', $server->ds_id],
        ]);

        return $taskQuery->whereIn('status', $statuses)->exists();
    }

    /**
     * @throws RecordExistExceptions
     * @throws InvalidServerStartCommandException
     * @throws EmptyServerStartCommandException
     */
    public function addServerStart(Server $server, int $runAftId = 0): int
    {
        $this->workingTaskNotExistOrFail($server, GdaemonTask::TASK_SERVER_START, 'Server start task is already exists');
        $this->serverCommandCorrectOrFail($server);

        return GdaemonTask::create([
            'run_aft_id'          => $runAftId,
            'dedicated_server_id' => $server->ds_id,
            'server_id'           => $server->id,
            'task'                => GdaemonTask::TASK_SERVER_START,
        ])->id;
    }

    /**
     * @throws RecordExistExceptions
     */
    public function addServerStop(Server $server, int $runAftId = 0): int
    {
        $this->workingTaskNotExistOrFail($server, GdaemonTask::TASK_SERVER_STOP, 'Server stop task is already exists');

        return GdaemonTask::create([
            'run_aft_id'          => $runAftId,
            'dedicated_server_id' => $server->ds_id,
            'server_id'           => $server->id,
            'task'                => GdaemonTask::TASK_SERVER_STOP,
        ])->id;
    }

    /**
     * @throws RecordExistExceptions
     * @throws InvalidServerStartCommandException
     * @throws EmptyServerStartCommandException
     */
    public function addServerRestart(Server $server, int $runAftId = 0): int
    {
        $this->workingTaskNotExistOrFail($server, GdaemonTask::TASK_SERVER_RESTART, 'Server restart task is already exists');
        $this->serverCommandCorrectOrFail($server);

        return GdaemonTask::create([
            'run_aft_id'          => $runAftId,
            'dedicated_server_id' => $server->ds_id,
            'server_id'           => $server->id,
            'task'                => GdaemonTask::TASK_SERVER_RESTART,
        ])->id;
    }

    /**
     * @throws RecordExistExceptions
     */
    public function addServerUpdate(Server $server, int $runAftId = 0): int
    {
        $this->workingTaskNotExistOrFail(
            $server,
            [GdaemonTask::TASK_SERVER_UPDATE, GdaemonTask::TASK_SERVER_INSTALL],
            'Server update/install task is already exists'
        );
        
        return GdaemonTask::create([
            'run_aft_id'          => $runAftId,
            'dedicated_server_id' => $server->ds_id,
            'server_id'           => $server->id,
            'task'                => GdaemonTask::TASK_SERVER_UPDATE,
        ])->id;
    }

    public function addServerInstall(Server $server, int $runAftId = 0)
    {
        $this->workingTaskNotExistOrFail(
            $server,
            [GdaemonTask::TASK_SERVER_UPDATE, GdaemonTask::TASK_SERVER_INSTALL],
            'Server update/install task is already exists'
        );

        return GdaemonTask::create([
            'run_aft_id'          => $runAftId,
            'dedicated_server_id' => $server->ds_id,
            'server_id'           => $server->id,
            'task'                => GdaemonTask::TASK_SERVER_INSTALL,
        ])->id;
    }

    /**
     * @throws RecordExistExceptions
     */
    public function addServerDelete(Server $server, int $runAftId = 0): int
    {
        $this->workingTaskNotExistOrFail($server, GdaemonTask::TASK_SERVER_DELETE, 'Server delete task is already exists');
        
        return GdaemonTask::create([
            'run_aft_id'          => $runAftId,
            'dedicated_server_id' => $server->ds_id,
            'server_id'           => $server->id,
            'task'                => GdaemonTask::TASK_SERVER_DELETE,
        ])->id;
    }

    public function addCmd($cmd, $dedicatedServerId, int $runAftId = 0)
    {
        return GdaemonTask::create([
            'run_aft_id'          => $runAftId,
            'dedicated_server_id' => $dedicatedServerId,
            'task'                => GdaemonTask::TASK_CMD_EXEC,
            'cmd'                 => $cmd,
        ])->id;
    }

    /**
     * @return mixed
     */
    public function getTasks(int $serverId, $tasks, array $statuses)
    {
        if (is_array($tasks)) {
            $taskQuery = GdaemonTask::whereIn(['task', $tasks])->where([['server_id', '=', $serverId]]);
        } else {
            $taskQuery = GdaemonTask::where([
                ['task', '=', $tasks],
                ['server_id', '=', $serverId],
            ]);
        }

        $taskQuery->whereIn('status', $statuses);

        return $taskQuery->get();
    }

    /**
     * @param int $serverId
     * @param $task
     * @return int
     */
    public function getFirstWaitingOrWorkingTaskId(int $serverId, $task): int
    {
        $tasks = $this->getTasks($serverId, $task, [GdaemonTask::STATUS_WAITING, GdaemonTask::STATUS_WORKING]);

        return (count($tasks) === 1)
            ? $tasks->first()->id
            : 0;
    }

    /**
     * @param GdaemonTask $gdaemonTask
     * @param string $output
     */
    public function concatOutput(GdaemonTask $gdaemonTask, string $output): void
    {
        if (empty($output)) {
            return;
        }

        $qoutedOutput = DB::connection()->getPdo()->quote($output);

        $dbDriver = DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($dbDriver === 'mysql') {
            $gdaemonTask->update(['output' => DB::raw("CONCAT(IFNULL(output,''), {$qoutedOutput})")]);
        } elseif ($dbDriver === 'sqlite' || $dbDriver === 'pgsql') {
            $gdaemonTask->update(['output' => DB::raw("COALESCE(output, '') || {$qoutedOutput}")]);
        } else {
            $gdaemonTask->update(['output' => $gdaemonTask->output . $output]);
        }
    }

    /**
     * @param GdaemonTask $gdaemonTask
     *
     * @throws GdaemonTaskRepositoryException
     */
    public function cancel(GdaemonTask $gdaemonTask): void
    {
        if ($gdaemonTask->status !== GdaemonTask::STATUS_WAITING) {
            throw new GdaemonTaskRepositoryException(__('gdaemon_tasks.cancel_fail_cannot_be_canceled'));
        }

        $gdaemonTask->status = GdaemonTask::STATUS_CANCELED;
        $gdaemonTask->save();
    }

    /**
     * @param Server $server
     * @param string|array $task task name
     * @param string $failMsg Failure message
     *
     * @throws RecordExistExceptions
     */
    private function workingTaskNotExistOrFail(Server $server, $task, $failMsg = 'Task is already exists'): void
    {
        if (is_array($task)) {
            $taskQuery = GdaemonTask::whereIn('task', $task)->where([['server_id', '=', $server->id]]);
        } else {
            $taskQuery = GdaemonTask::where([
                ['task', '=', $task],
                ['server_id', '=', $server->id],
                ['dedicated_server_id', '=', $server->ds_id],
            ]);
        }

        $taskExist = $taskQuery->whereIn('status', [
            GdaemonTask::STATUS_WAITING,
            GdaemonTask::STATUS_WORKING,
        ]);

        if ($taskExist) {
            throw new RecordExistExceptions($failMsg);
        }
    }

    /**
     * @param Server $server
     *
     * @throws InvalidServerStartCommandException
     * @throws EmptyServerStartCommandException
     */
    private function serverCommandCorrectOrFail(Server $server): void
    {
        if (empty($server->start_command)) {
            throw new EmptyServerStartCommandException(__('gdaemon_tasks.empty_server_start_command'));
        }
    }
}
