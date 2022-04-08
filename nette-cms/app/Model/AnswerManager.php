<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;
use Nette\Database\Context;

class AnswerManager extends DatabaseManager
{
    const
        TABLE_NAME = 'answer',
        COLUMN_ID = 'id',
        COLUMN_ON_HOMEPAGE = 'on_homepage',
        COLUMN_URL = 'url';

    // private $picturePath;

    private $questionManager;

    public function __construct(Context $database)
    {
        parent::__construct($database);
    }

    public function getAnswer(string $url, string $columns = NULL): IRow
    {
        return $this->database->table(self::TABLE_NAME)
            ->select($columns ? $columns : '*')
            ->where(self::COLUMN_URL, $url)
            ->fetch();
    }

    public function getAnswerFromId(int $id, string $columns = NULL): IRow
    {
        return $this->database->table(self::TABLE_NAME)
            ->select($columns ? $columns : '*')
            ->where(self::COLUMN_ID, $id)
            ->fetch();
    }

    public function getAnswers(array $parameters): Selection
    {
        $answers = $this->database->table(self::TABLE_NAME);

        if (!empty($parameters['question_id']))
            $answers->where(':' . self::TABLE_NAME . '_' . QuestionManager::TABLE_NAME .
                '.' . QuestionManager::TABLE_NAME .
                '.' . QuestionManager::COLUMN_ID,
                $parameters['question_id']
            );

        return $answers;
    }

    public function getAllAnswers()
    {
        return $this->database->query('SELECT answer.id, answer.title, answer.url, answer.date_add, answer_question.question_id, question.name FROM answer JOIN answer_question ON answer.id = answer_question.answer_id LEFT JOIN question ON question.id = answer_question.question_id');
    }

    public function getAllAnswersQuestion()
    {
        return $this->database->query('SELECT answer.id, answer.title, answer.url, answer.date_add, answer_question.question_id, question.name as questionName FROM answer JOIN answer_question ON answer.id = answer_question.answer_id LEFT JOIN question ON question.id = answer_question.question_id WHERE question.state = ?', 1)->fetchPairs();
    }

    public function saveAnswer(array $values): IRow
    {
        $answerData = [
            'title' => $values['title'],
            'url' => $values['url'],
        ];

        if (!empty($values['id'])) {
            $answerQuestionData = [
                'answer_id' => $values['id'],
                'question_id' => $values['questions'],
            ];
            $answer = $this->database->table(self::TABLE_NAME)
                ->wherePrimary($values['id']);

            $answer->update($answerData);
            $answer = $answer->fetch();

            $this->database->table('answer_question')
                ->where('answer_id = ?', $answer->id)
                ->delete();
        } else {
            $answer = $this->database->table('answer')
                ->insert($answerData);

            $answer_id = $answer->id;
            $answerQuestionData = [
                'answer_id' => $answer_id,
                'question_id' => $values['questions'],
            ];
        }

        if (!empty($values['questions'])) {
            $this->database->table('answer_question')->insert($answerQuestionData);
        }

        return $answer;
    }

    public function removeAnswer(string $url)
    {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_URL, $url)->delete();
    }

    public function getAnswersCount()
    {
        return $this->database->table(self::TABLE_NAME)->count('*');
    }
}