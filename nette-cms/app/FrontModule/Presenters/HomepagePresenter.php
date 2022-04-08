<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use Nette;
use Nette\Application\UI\Presenter;
use Nette\Database\Context;
use App\Model\AnswerManager;
use App\Model\ResultManager;
use App\Model\QuestionManager;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;


final class HomepagePresenter extends Presenter
{
    private $database;

    private $answerManager;

    private $questionManager;

    private $resultManager;

    public function __construct(Context $database, AnswerManager $answerManager, QuestionManager $questionManager, ResultManager $resultManager)
    {
        parent::__construct();
        $this->database = $database;
        $this->answerManager = $answerManager;
        $this->questionManager = $questionManager;
        $this->resultManager = $resultManager;
    }
    
    public function renderDefault(): void
    {
        $this->template->answers = $this->answerManager->getAllAnswersQuestion();
        $this->template->activeQuestions = $this->questionManager->getActiveQuestion();

    }

    protected function createComponentEditorForm(): Form
    {
        $form = new Form;
        $form->setRenderer(new BootstrapVerticalRenderer);
        $form->addHidden('id');
        $answers = $this->answerManager->getAllAnswersQuestion();
        $form->addSelect('answers', 'Odpověď:', $answers)
            ->setPrompt('Zvolte odpověď');
        $form->addSubmit('save', 'Uložit odpověď');
        $form->onSuccess[] = function (Form $form, Array $values) {
            try {
                $this->resultManager->saveResult($values);
                $this->flashMessage('Odpověď jsem uložil.');
                $this->redirect('Homepage:');
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Již jste odpovídal na tuto anketu');
            }
        };
        return $form;
    }
}
