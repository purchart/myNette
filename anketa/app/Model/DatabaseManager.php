<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\SmartObject;

/**
 * Zakladni model pro vsechny ostatni databazove modely aplikace.
 * POskytuje pristup k praci s databazi.
 * @package App\Model
 */
class DatabaseManager
{
    use SmartObject;

    /** @var Explorer sluzba pro praci s databazi. */
    protected Explorer $database;

    /** Konstruktor s injektovanou sluzbou pro praci s databazi.
     * @param Explorer $database Automaticky injektovana Nette sluzba pro praci s databazi
     */
    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }
}