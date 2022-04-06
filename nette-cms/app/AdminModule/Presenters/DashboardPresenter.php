<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette;
use Nette\Database\Context;

final class DashboardPresenter extends BaseAdminPresenter
{
    private $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function renderDefault(): void
    {
        $this->template->text = 'Správa anket';
    }
}

