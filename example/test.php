<?php

require __DIR__.'/vendor/autoload.php';

use WF\{Stamp\Draw, Exception\Trash, Client, Enums\Warface};

try {

    $image = (new Draw(
        (new Client)->get('Эдия', Warface::ALPHA)
            ->edit([''])
            ->addAchievements(['marks' => 417, 'strips' => 8018])
    ))->init();

    header('content-type: image/png');
    echo $image;

} catch (ImagickException | Trash $e) {
    var_dump($e);
}

