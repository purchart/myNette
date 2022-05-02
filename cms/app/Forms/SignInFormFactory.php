<?php

declare(strict_types=1);

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\SmartObject;
use Nette\Security\User;
use Nette\Security\AuthenticationException;

final class SignInFormFactory
{
    use SmartObject;

    /** @var FormFactory */
    private $formFactory;

    /** @var User */
    private $user;

    public function __construct(FormFactory $formFactory, User $user)
    {
        $this->formFactory = $formFactory;
        $this->user = $user;
    }

    public function create(callable $onSuccess): Form
    {
        $form = $this->formFactory->create();
        $form->addEmail('email', 'Email:')
            ->setRequired('Zadejte prosim email.');
        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zadejte heslo.');
        $form->addSubmit('send', 'Přihlásit');
        $form->onSuccess[] = function (Form $form, \StdClass $values) use ($onSuccess): void {
            try {
                $this->user->login($values->email, $values->password);
            } catch (AuthneticationException $e) {
                $form->addError('Je nutne zadat spravne prihlasovaci udaje');
                return;
            }
            $onSuccess();
        };

        return $form;        
    }

}