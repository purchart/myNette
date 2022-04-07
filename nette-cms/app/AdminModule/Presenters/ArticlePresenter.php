<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\ArticleManager;
use App\Model\CategoryManager;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class ArticlePresenter extends BaseAdminPresenter {
    
    private $articleManager;

    private $categoryManager;

    public function __construct(ArticleManager $articleManager, CategoryManager $categoryManager)
    {
        parent::__construct();
        $this->articleManager = $articleManager;
        $this->categoryManager = $categoryManager;
    }

    public function renderList()
    {
        $this->template->articles = $this->articleManager->getAllArticles();
    }

    public function actionRemove(string $url = null)
    {
        $this->articleManager->removeArticle($url);
        $this->flashMessage('Otázku k anketě jsem smazal.');
        $this->redirect('Article:list');
    }

    public function actionEditor(string $url = null)
    {
        if ($url) {
            if (!($article = $this->articleManager->getArticle($url))) {
                $this->flashMessage('Otázka k anketě nebyla nalezena.');
            } else {
                $this['editorForm']->setDefaults($article);
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
        // $form->addUpload('picture', 'Obrázek: ')
        //     ->setRequired(false)
        //     ->addCondition(Form::FILLED)
        //     ->addRule(Form::IMAGE);
        // $form->addText('short_description', 'Popisek')
        //     ->setRequired();
        // $form->addTextArea('description', 'Obsah');
        $categories = $this->categoryManager->getAllCategory();
        $form->addSelect('categories', 'Anketa: ', $categories)
            ->setPrompt('Zvolte anketu');
        $form->addSubmit('save', 'Uložit odpověď');
        $form->onSuccess[] = function (Form $form, Array $values) {
            try {
                $this->articleManager->saveArticle($values);
                $this->flashMessage('Odpověď jsem uložil.');
                $this->redirect('Article:list');
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Odpověď s touto url již existuje.');
            }
        };
        return $form;
    }
}

