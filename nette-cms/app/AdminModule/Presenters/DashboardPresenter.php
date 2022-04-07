<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette;
use App\Model\CategoryManager;

final class DashboardPresenter extends BaseAdminPresenter
{
    private $database;

    private $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        parent::__construct();
        $this->categoryManager = $categoryManager;
    }

    public function renderDefault(): void
    {
        $this->template->categoryTotal = $this->categoryManager->getCategoriesCount();
    }
}

