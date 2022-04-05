<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\Coremodule\Model\ArticleManager;
use App\Presenters\BasePresenter;
use Nette\Application\BadRequestException;

/**
 * Presenter pro vykreslovani clanku
 * @package App\CoreModule\Presenters
 */
class ArticlePresenter extends BasePresenter
{
    /** @var string URL vychoziho clanku */
    private $defaultArticleUrl;

    /** @var ArticleManager model pro spravu clanku */
    private $articleManager;

    public function __contruct(string $defaultArticleUrl, ArticleManager $articleManager)
    {
        parent::__construct();
        $this->defaultArticleUrl = $defaultArticleUrl;
        $this->articleManager = $articleManager;
    }

    /**
     * nacte a preda clanek do sablony podle URL
     * @param string|null $url URL clanku
     * @throws BadRequestException jestli clanek s danou url nebyl nalezen
     */
    public function renderDefault(string $url = null)
    {
        if(!$url)$this->url = $this->defaultArticleUrl; // pokud neni url vezme se url vychoziho clanku

        //pokusi se nacist clanek do sablony pokud ho nenacte vyhodi chybu error 404
        if(!($article = $this->articleManager->getArticle($url)))
            $this->error(); //vyhazuje vyjimku BadRequestException
        
        $this->template->article = $article;
    }

    /**
     * nacte a preda seznam clanku do sablony
     */
    public function renderList()
    {
        $this->template->articles = $this->articleManager->getArticles();
    }

    /**
     * Odstrani clanek 
     * @param string|null $urt URL clanku
     * @throws abortException
     */
    public function actionRemove(string $url = null)
    {
        $this->articleManager->removeArticle($url);
        $this->flashMessage('clanek byl uspesne smazan');
        $this->redirect('Article:List');
    }

    
}
