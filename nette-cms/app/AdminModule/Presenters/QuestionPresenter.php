<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\QuestionManager;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class QuestionPresenter extends BaseAdminPresenter
{
    private $questionManager;

    public function __construct(QuestionManager $questionManager)
    {
        parent::__construct();
        $this->questionManager = $questionManager;
    }

    public function renderList()
    {
        $this->template->questions = $this->questionManager->getQuestions();
    }

    public function actionRemove(string $url = null)
    {
        $this->questionManager->removeQuestion($url);
        $this->flashMessage('Anketa byla úspěšně odstraněna.');
        $this->redirect('Question:list');
    }

    public function actionEditor(string $url = null)
    {
        if ($url) {
            if (!($question = $this->questionManager->getQuestion($url))) {
                $this->flashMessage('Anketa nebyla nalezena.');
            } else {
                $this['editorForm']->setDefaults($question);
            }
        }
    }

    protected function createComponentEditorForm(): Form {
        $form = new Form;
        $form->setRenderer(new BootstrapVerticalRenderer);
        $form->addHidden('id');
        $form->addText('name', 'Název')->setRequired();
        $form->addText('url', 'URL')->setRequired();
        $state = ['1' => 'Aktivní anketa', '0' => 'Neaktivní anketa' ];
        $form->addSelect('state', 'Stav', $state);
        $form->addSubmit('save', 'Uložit kategorii');
        $form->onSuccess[] = function (Form $form, Array $values) {
            try {
                $activeQuestionCount = $this->questionManager->getActiveQuestionCount();
                if ($activeQuestionCount > 0 && $values['state'] == 1) $this->flashMessage('Jen jedna anketa muze byt aktivni.');
                else {
                    $this->questionManager->saveQuestion($values);
                    $this->flashMessage('Anketa byla úspěšně uložena.');
                    $this->redirect('Question:list');
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Anketa s timto nazvem jiz existuje.');
            }
        };
        return $form;
    }    
}