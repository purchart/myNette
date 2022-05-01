<?php
declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette\Application\UI\Form;
use Nette\Application\AbortException;
use App\Model\ArticleManager;
use App\Model\CategoryManager;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;
use Nette\Database\UniqueConstraintViolationException;

class ArticlePresenter extends BaseAdminPresenter
{
    /** @var ArticleManager */
    private $articleManager;

    /** @var CategoryManager */
    private $categoryManager;

    public function __construct(ArticleManager $articleManager, CategoryManager $categoryManager)
    {
        parent::__construct();
        $this->articleManager = $articleManager;
        $this->categoryManager = $categoryManager;
    }

    public function renderList()
    {
        return $this->template->articles = $this->articleManager->getAllArticles();
    }

    public function actionRemove(string $url = null)
    {
        $this->articleManager->removeArticle();
        $this->flashMessage('Článek jsem úspěšně smazal.');
        $this->redirect('Article:list');
    }

    public function actionEditor(string $url = null)
    {
        if ($url){
            if (!($article = $this->articleManager->getArticle($url))){
                $this->flashMessage('Článek jsem nenalezl.');
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
        $form->addText('title', 'Titulek')
            ->setRequired();
        $form->addText('url', 'URL')
            ->setRequired();
        $form->addUpload('picture', 'Obrázek:')
            ->setRequired(false)
            ->addCondition(Form::FILLED)
            ->addRule(Form::IMAGE);
        $form->addText('short_description', 'Popis')
            ->setRequired();
        $form->addTextArea('description', 'Obsah');
        $categories = $this->categoryManager->getAllCategory();
        $form->addSelect('categories', 'Kategorie:', $categories)
            ->setPrompt('Zvolte kategorii');
        $form->addSubmit('save', 'Uložit článek');
        $form->onSuccess[] = function (Form $form, Array $values){
            try {
                $this->articleManager->saveArticle($values);
                $this->flashMessage('Článek jsem uložil');
                $this->redirect('Article:list');
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Článek s touto url již existuje.');
            }
        };
        return $form;
    }
}
