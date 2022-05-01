<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\CategoryManager;
use Nette\Application\UI\Form;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;
use Nette\Database\UniqueConstraintViolationException;

/**
 * @package App\AdminModule\Presenters
 */
class CategoryPresenter extends BaseAdminPresenter
{
    /** @var CategoryManager */
    private $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        parent::__construct();
        $this->categoryManager = $categoryManager;
    }

    public function renderList()
    {
        $this->template->categories = $this->categoryManager->getCategories();
    }

    public function actionRemove(string $url = null)
    {
        $this->categoryManager->removeCategory($url);
        $this->flashMessage('Kategorii jsem smazal.');
        $this->redirect('Category:list');
    }

    public function actionEditor(string $url = null)
    {
        if ($url){
            if (!($category = $this->categoryManager->getCategory($url))){
                $this->flashMessage('Kategorii jsem nenasel.');
            } else {
                $this['editorForm']->setDefaults($category);
            }
        }
    }

    protected function createComponentEditorForm(): Form
    {
        $form = new Form;
        $form->setRenderer(new BootstrapVerticalRenderer);
        $form->addHidden('id');
        $form->addText('name', 'Název')->setRequired();
        $form->addText('url', 'URL')->setRequired();
        $form->addSubmit('save', 'Uložit kategorii');
        $form->onSuccess[] = function(Form $form, Array $values){
            try {
                $this->categoryManager->saveCategory($values);
                $this->flashMessage('Kategorii jsem uložil.');
                $this->redirect('Category:list');
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Kategorie s touto URL adresou již existuje.');
            }
        };
        return $form;
        
    }
}