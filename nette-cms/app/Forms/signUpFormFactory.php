<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;
use Nette\SmartObject;

final class SignUpFormFactory
{
    use SmartObject;

    private const PASSWORD_MIN_LENGTH = 6;

    private $factory;

    private $userManager;

    public function __construct(FormFactory $factory, Model\UserManager $userManager)
    {
        $this->factory = $factory;
        $this->userManager = $userManager;
    }

    public function create(callable $onSuccess): Form
    {
        $form = $this->factory->create();
        $form->addText('firstname', 'Jméno:')
            ->setRequired('Zdadejte prosím jméno.');
        $form->addText('lastname', 'Příjmení:')
            ->setRequired('Zadejte prosím příjmení.');
        $form->addEmail('email', 'Email:')
            ->setRequired('Zadejte prosím email.');
        $form->addPassword('password', 'Heslo:')
            ->setOption('description', sprintf('Heslo musí mít minimálně %d znaků.', self::PASSWORD_MIN_LENGTH))
            ->setRequired('Zvolte prosím heslo.')
            ->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH);
        $form->addPassword('password_repeat', 'Heslo znovu:')
            ->setOmitted()
            ->setRequired(false)
            ->addRule($form::EQUAL, 'Hesla nesouhlasí.', $form['password']);
        $roles = [
            'Role' => [
                'member' => 'Správce',
                'guest' => 'Administrátor',
            ]
        ];
        $form->addSelect('role', 'Role:', $roles)
            ->setPrompt('vyberte roli');
        
        $form->addSubmit('send', 'Vytvořit uživatele');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void
        {
            try {
                $this->userManager->add($values->firstname, $values->lastname, $values->email, $values->password, $values->role);
            } catch (Model\DuplicateNameException $e) {
                $form['email']->addError('Tento email v databázi existuje.');
                return;
            }
            $onSuccess();
        };
        return $form;
         
    }



}

class DuplicateNameException extends \Exception
{
    protected $message = 'Uživatel je s tímto emailem již zaregistrovaný.';
}