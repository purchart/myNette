<?php

declare(strict_types=1);

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use App\Forms\FormFactory;

final class SignInFormFactory
{
    use Nette\SmartObject;

    private $factory;

    private $user;

    public function __construct(FormFactory $factory, User $user)
    {
        $this->factory = $factory;
        $this->user = $user;
    }

    public function create(callable $onSuccess): Form
    {
        $form = $this->factory->create();
        $form->addEmail('email', 'Email:')
            ->setRequired('Zadejte prosim vas email');
        $form->addPassword('password', 'Password:')
            ->setRequired('Zadejte prosim vase heslo');
        $form->addSubmit('send', 'Prihlasit');
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
            try {
                $this->user->login($values->email, $values->password);
            } catch (Nette\Security\AuthenticationException $e) {
                $form->addError('Je nutne zadat spravne prihlasovaci udaje');
                return;
            }
                $onSuccess();
        };

        return $form;
    }
}