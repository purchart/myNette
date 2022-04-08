<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\AnswerManager;
use App\Model\QuestionManager;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class AnswerPresenter extends BaseAdminPresenter {
    
    private $answerManager;

    private $questionManager;

    public function __construct(AnswerManager $answerManager, QuestionManager $questionManager)
    {
        parent::__construct();
        $this->answerManager = $answerManager;
        $this->questionManager = $questionManager;
    }

    public function renderList()
    {
        $this->template->answers = $this->answerManager->getAllAnswers();
    }

    public function actionRemove(string $url = null)
    {
        $this->answerManager->removeAnswer($url);
        $this->flashMessage('Otázku k anketě jsem smazal.');
        $this->redirect('Answer:list');
    }

    public function actionEditor(string $url = null)
    {
        if ($url) {
            if (!($answer = $this->answerManager->getAnswer($url))) {
                $this->flashMessage('Otázka k anketě nebyla nalezena.');
            } else {
                $this['editorForm']->setDefaults($anser);
            }
        }
    }

    protected function createComponentEditorForm(): Form
    {
        $form = new Form;
        $form->setRenderer(new BootstrapVerticalRenderer);
        $form->addHidden('id');
        $form->addText('title', 'Odpověď')
            ->setRequired();
        $form->addText('url', 'URL')
            ->setRequired();
        $questions = $this->questionManager->getAllQuestion();
        $form->addSelect('questions', 'Anketa: ', $questions)
            ->setPrompt('Zvolte anketu');
        $form->addSubmit('save', 'Uložit odpověď');
        $form->onSuccess[] = function (Form $form, Array $values) {
            try {
                $this->answerManager->saveAnswer($values);
                $this->flashMessage('Odpověď jsem uložil.');
                $this->redirect('Answer:list');
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Odpověď s touto url již existuje.');
            }
        };
        return $form;
    }
}

