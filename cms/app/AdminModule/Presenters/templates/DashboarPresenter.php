<?php

declare (strict_types=1);

namespace App\AdminModule\Presenters;

use Nette;
use Nette\Application\UI\Presenter;
use Nette\Database\Context;

final class DashboardPresenter extends Presenter
{
    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function renderDefault(): void
    {
        $this->template->text = "Dashboard";
    }


}
