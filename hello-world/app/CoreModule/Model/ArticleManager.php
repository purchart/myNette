<?php

declare(strict_types=1);

namespace App\CoreModule\Model;

use App\Model\DatabaseManager;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

/** Model pro spravu clanku v redakcnim systemu.
 * @package App\CoreModule\Model
 */
class ArticleManager extends DatabaseManager{
    /** konstanty pro praci s databazi */
    const
        TABLE_NAME = 'article',
        COLUMN_ID = 'article_id',
        COLUMN_URL = 'url';
    
    /** Vrati seznam vsech clanku v databazi serazeny sestupne od naposledy pridaneho.
     * @return Selection seznam vsech clanku
     */
    public function getArticles()
    {
        return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_ID . 'DESC');
    }

    /**
     * Vrati clanek z databaze podle jeho url
     * @param string $url URL clanku
     * @return false|ActiveRow prvni clanek, ktery odpovida URL nebo false pokud clanek s danou URL neexistuje
     */
    public function getArticle($url)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_URL, $url)->fetch();
    }

    /**
     * ulozi clanek do systemu
     * Pokud neni nastaveno ID vlozi novy clanek, jinak provede editaci clanku s danym ID
     * @param array|ArrayHash $article clanek
     */
    public function saveArticle(ArrayHash $article)
    {
        if (empty($article[self::COLUMN_ID])) {
            unset($article[self::COLUMN_ID]);
            $this->database->table(self::TABLE_NAME)->insert($article);
        } else {
            $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $article[self::COLUMN_ID])->update($article);
        }
    }

    /**
     * odstrani clanek s danou URL
     * @param string $url URL clanku
     */
    public function removeArticle(string $url)
    {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_URL, $url)->delete();
    }
}