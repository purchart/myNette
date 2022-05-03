<?php
declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\ContactManager;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Nette\Application\UI\Form;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;
use Nette\Utils\ArrayHash;

/**
 * @package App\FrontModule\Presenters
 */
class ContactPresenter extends BaseFrontPresenter
{
    const 
        MIN_LENGTH_MESSAGE = 10;

    /** @var string */
    private $contactEmail;

    /** @var IMailer */
    private $mailer;

    /** @var ContactManager */
    private $contactManager;

    /**
     * @param string $contactEmail
     * @param IMailer $mailer
     * @param ContactManager $contactmanager
     */
    public function __construct(string $contactEmail, IMailer $mailer, ContactManager $contactManager) {
        parent::__construct();
        $this->contactEmail = $contactEmail;
        $this->mailer = $mailer;
        $this->contactManager = $contactManager;
    }

    public function renderDefault()
    {
        $this->template->contact = $this->contactManager->getContact();
    }

    protected function createComponentContactForm(): Form
    {
        $form = new Form;
        $form->setRenderer(new BootstrapVerticalRenderer);
        $form->getElementPrototype()->setAttribute('novalidate', true);
        $form->addEmail('email', 'Vaše emailová adresa:')->setRequired();
        $form->addText('y', 'Zdejte aktuální rok.')->setOmitted()->setRequired()
            ->addRule(Form::EQUAL, 'Aktuální rok je špatně.', date("Y"));
        $form->addTextArea('message', 'Zpráva')->setRequired()
            ->addRule(Form::MIN_LENGTH, "Zpráva musí mít minimálně %d znaků.", self::MIN_LENGTH_MESSAGE);
        $form->addSubmit('send', 'Odeslat');
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $mail = new Message;
                $mail->setFrom($values->email)
                    ->addTo($this->contactEmail)
                    ->setSubject('Email z webu')
                    ->setBody($values->message);
                $this->mailer->send($mail);
                $this->flashMessage('Email jsem odeslal.');
                $this->redirect('this');
            } catch (SendException $e) {
                $this->flashMessage('Email se mi nepodařilo odeslat.');
            }
        };
        return $form;

    }
}