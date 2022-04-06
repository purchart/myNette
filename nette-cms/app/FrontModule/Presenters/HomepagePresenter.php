<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use Nette;
use Nette\Application\UI\Presenter;
use Nette\Database\Context;


final class HomepagePresenter extends Presenter
{
    private $database;

    public function __construct(Context $database)
    {
        parent::__construct();
        $this->database = $database;
    }
    public function renderDefault(): void
    {
        $this->template->articles = $this->database->table('article')
            ->order('date_add DESC')
            ->limit(5);
    }
}
