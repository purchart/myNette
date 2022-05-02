<?php
declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\userManager;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use App\Forms\SignUpFormFactory;

/**
 * @package App\AdminModule\Presenters
 */
class UserPresenter extends BaseAdminPresenter
{
    /** @var SignUpFormFactory */
    private $signUpFormFactory;

    /** @var UserManager */
    private $userManager;

    public function __construct(UserManager $userManager, SignUpFormFactory $signUpFormFactory)
    {
        parent::__construct();
        $this->userManager = $userManager;
        $this->signUpFormFactory = $signUpFormFactory;
    }

    public function renderList()
    {
        $this->template->users = $this->userManager->getUsers();

    }

    public function actionRemove(int $id = null)
    {
        $this->userManager->removeUser($id);
        $this->flashMessage('Smazal jsem uživatele.');
        $this->redirect('User:list');
    }

    protected function createComponentSignUpForm(): Form
    {
        return $this->signUpFormFactory->create( function ():Void {
            $this->redirect('User:list');
        });
    } 
}