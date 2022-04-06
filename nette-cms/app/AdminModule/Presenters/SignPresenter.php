<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette\Application\UI\Form;
use App\Forms\SignInFormFactory;

final class SignPresenter extends BaseAdminPresenter
{
    public $backlink = '';

    private $signInFormFactory;

    public function __construct(SignInFormFactory $signInFormFactory)
    {
        $this->signInFormFactory = $signInFormFactory;
    }

    protected function createComponentSignInForm(): Form
    {
        return $this->signInFormFactory->create(function (): void {
            $this->restoreRequest($this->backlink);
            $this->redirect('Dashboard:');
        });
    }

    public function actionOut(): void
    {
        $this->user->logout();
    }
}

