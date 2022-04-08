<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;
use Nette\Database\Context;
use Nette\Http\Request;

class ResultManager extends DatabaseManager
{
    const
        TABLE_NAME = 'result',
        COLUMN_ID = 'id',
        COLUMN_URL = 'url';

    // private $picturePath;

    private $questionManager;

    private $httpRequest;

    public function __construct(Context $database, Request $httpRequest)
    {
        parent::__construct($database);
        $this->httpRequest = $httpRequest;

    }

    public function saveResult(array $values): IRow
    {
        $resultData = [
            'name' => null,
            'answer' => $values['answers'],
            'ip' => $this->httpRequest->getRemoteAddress(),
        ];

        $result = $this->database->table(self::TABLE_NAME)
            ->insert($resultData);

        return $result;
    }
}