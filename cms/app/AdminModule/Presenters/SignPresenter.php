<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Forms\SignInFormFactory;
use Nette\Application\UI\form;

final class SignPresenter extends BaseAdminPresenter
{
    /** @var SignInFormFactory */
    private $signInFormFactory;

    /** @persistent */
    public $backlink = '';

    public function __construct(SignInFormFactory $signInFormFactory)
    {
        $this->signInFormFactory = $signInFormFactory;
    }

    public function createComponentSignInForm(): Form
    {
        return $this->signInFormFactory->create(function (): void {
            $this->restoreRequest($this->backlink);
            $this->redirect('Dashboard:');
        });
    }

    public function actionOut(): Void
    {
        $this->getUser()->logout();
    }


}
