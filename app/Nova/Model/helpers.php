<?php

use App\Nova\Model\PloiManager;

function ploi(): PloiManager
{
    return app(PloiManager::class);
}
