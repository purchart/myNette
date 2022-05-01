<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Forms\FormFactory;
use Nette\Application\UI\Presenter;
use Nette\Appllication\AbortException;

/**
 * BaseAdminPresenter for all Presenters
 */
abstract class BaseAdminPresenter extends Presenter
{
    /** @var FormFactory */
    protected $formFactory;

    public final function injectFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    protected function startup(): Void
    {
        parent::startup();
        if (!$this->getUser()->isAllowed($this->getName(), $this->getAction())){
            $this->flashMessage('Pro tuto akci nemas dostatecna opravneni.');
            if (!$this->getUser()->isLoggedIn()){
                $this->redirect('Sign:in');
            } else {
                $this->redirect('Dashboard:');
            }
        }
    }

    public function actionLogout(){
        $this->user->logout();
        $this->redirect('login');
    }

    public function beforeRender(): Void {
        parent::beforeRender();
        if ($this->user->isLoggedIn()) {
            $this->template->firstname = $this->user->identity->firstname;
            $this->template->lastname = $this->user->identity->lastname;
            $this->template->email = $this->user->identity->email;
            $this->template->role = $this->user->identity->role;
            
        }
    }
}