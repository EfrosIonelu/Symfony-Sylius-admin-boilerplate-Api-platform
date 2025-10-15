<?php

namespace App\Importer\Main;

interface DeleteCacheInterface
{
    // if is processed async
    public function deleteCache(): void;
}
