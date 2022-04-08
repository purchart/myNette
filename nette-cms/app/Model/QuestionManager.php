<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class QuestionManager extends DatabaseManager
{
    const
        TABLE_NAME = 'question',
        COLUMN_ID = 'id',
        COLUMN_TITLE = 'name',
        COLUMN_URL = 'url',
        COLUMN_STATE = 'state';

    public function getAnswerQuestions(int $id)
    {
        return $this->database->table(AnswerManager::TABLE_NAME . '_' . self::TABLE_NAME)
            ->select(self::COLUMN_ID)
            ->where(AnswerManager::COLUMN_ID, $id)
            ->fetchPairs(null, self::COLUMN_ID);
    }

    public function updateAnswerQuestions(int $id, array $categories)
    {
        $this->database->table(AnswerManager::TABLE_NAME . '_' . self::TABLE_NAME)
            ->where(AnswerManager::COLUMN_ID, $id)->delete();
        $rows = array();
        foreach ($categories as $question) {
            $rows[] = array(
                AnswerManager::COLUMN_ID => $id,
                self::COLUMN_ID => $question
            );
        }
        $this->database->table(AnswerManager::TABLE_NAME . '_' . self::TABLE_NAME)->insert($rows);
    }

    public function getAnswerQuestion(int $id)
    {
        if ($row = $this->database->table(AnswerManager::TABLE_NAME . '_' . self::TABLE_NAME)
            ->where(AnswerManager::COLUMN_ID, $id)
            ->fetch()
        )
            return $row->ref(self::TABLE_NAME)->url;
        else
            return '';
    }

    public function getQuestions(): Selection
    {
        return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_ID . ' ASC');
    }

    public function getAllQuestion()
    {
        return $this->database->table(self::TABLE_NAME)->fetchPairs(self::COLUMN_ID, self::COLUMN_TITLE);
    }

    public function getQuestion(string $url): ActiveRow
    {
        return $this->database->table(self::TABLE_NAME)
            ->where(self::COLUMN_URL, $url)
            ->fetch();
    }

    public function getActiveQuestion()
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_STATE, 1)->order(self::COLUMN_ID . ' ASC');
    }
    
    public function getActiveQuestionCount()
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_STATE, 1)->count('*');
    }

    public function removeQuestion(string $url)
    {
        $this->database->table(self::TABLE_NAME)
            ->where(self::COLUMN_URL, $url)
            ->delete();
    }

    public function saveQuestion(array $question)
    {
        if (empty($question[self::COLUMN_ID])) {
            unset($question[self::COLUMN_ID]);
            $this->database->table(self::TABLE_NAME)->insert($question);
        } else {
            $this->database->table(self::TABLE_NAME)
                ->where(self::COLUMN_ID, $question[self::COLUMN_ID])
                ->update($question);
        }
    }

    public function getQuestionsCount()
    {
        return $this->database->table(self::TABLE_NAME)->count('*');
    }
}