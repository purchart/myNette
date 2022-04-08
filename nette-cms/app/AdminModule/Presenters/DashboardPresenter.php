<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette;
use App\Model\QuestionManager;

final class DashboardPresenter extends BaseAdminPresenter
{
    private $database;

    private $questionManager;

    public function __construct(QuestionManager $questionManager)
    {
        parent::__construct();
        $this->questionManager = $questionManager;
    }

    public function renderDefault(): void
    {
        $this->template->questionTotal = $this->questionManager->getQuestionsCount();
    }
}

