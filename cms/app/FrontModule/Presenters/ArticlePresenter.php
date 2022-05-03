<?php
declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\ArticleManager;
use App\Model\CommentManager;
use App\Model\CategoryManager;
use App\Components\Comments;

class ArticlePresenter extends BaseFrontPresenter
{
    /** @var ArticleManager */
    private $articleManager;

    /** @var CommentManager */
    private $commentManager;

    /** @var integer */
    private $article_id;

    /** 
     * @package ArticleManager $articleManager
     * @package CommentManager $commentManager
     */
    public function __construct(Articlemanager $articleManager, CommentManager $commentManager)
    {
        parent::__construct();
        $this->articleManager = $articleManager;
        $this->commentManager = $commentManager;
    }

    public function actionDetail(int $id)
    {
        $this->article_id = $id;
    }

    public function renderDefault(string $url)
    {
        $parameters = $this->getParameters();
        if ($url && ($category = $this->categoryManager->getCategory($url))) {
            $parameters['category_id'] = $category->id;
        } elseif ($url) {
            $this->flashMessage('Kategorie neexistuje');
            $this->redirect('Font:Homepage:');
        }
        $products = $this->articleManager->getArticles($parameters);
        $this->template->categoryName = isset($category) ? $category->name : 'Články';
        $this->template->articles = $products;
    }

    public function renderDetail(int $id)
    {
        try {
            $this->template->article = $this->articleManager->getArticleFromId($id);
        } catch (\Exception $e) {
            $this->error('Článek nebyl nalezen.');
        }
    }

    public function createComponentComments(): Comments
    {
        return new Comments($this->commentManager, $this->article_id);
    }


}