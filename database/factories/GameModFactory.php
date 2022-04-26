<?php

namespace Database\Factories;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Gameap\Models\GameMod;
use Faker\Generator as Faker;

$factory->define(GameMod::class, function (Faker $faker) {
    $randomGameMod = GameMod::all()->random();

    return [
        'game_code'                     => $randomGameMod->game_code,
        'name'                          => $faker->name,
        'fast_rcon'                     => '',
        'vars'                          => '',
        'remote_repository'             => '',
        'local_repository'              => '',
        'start_cmd_nix'       => '',
        'start_cmd_win'     => '',
        'kick_cmd'                      => '',
        'ban_cmd'                       => '',
        'chname_cmd'                    => '',
        'srestart_cmd'                  => '',
        'chmap_cmd'                     => '',
        'sendmsg_cmd'                   => '',
        'passwd_cmd'                    => '',
    ];
});
