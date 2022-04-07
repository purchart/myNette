<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Context;

abstract class DatabaseManager
{
    use Nette\SmartObject;

    protected $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }
}
