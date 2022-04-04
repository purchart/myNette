<?php

declare(strict_types=1);

namespace App\Model;

use Nette\SmartObject;

/**
 * Model operací kalkulačky.
 * @package App\Model
 */
class CalculatorManager
{
    use SmartObject;

    /** Definice konstant pro operace. */
    const
        ADD = 1,
        SUBTRACT = 2,
        MULTIPLY = 3,
        DIVIDE = 4;

    /**
     * Getter pro existující operace.
     * @return array asociativní pole konstant pro operace a jejich slovního pojmenování
     */
    public function getOperations()
    {
        return array(
            self::ADD => 'Sčítání',
            self::SUBTRACT => 'Odčítání',
            self::MULTIPLY => 'Násobení',
            self::DIVIDE => 'Dělení'
        );
    }

    /**
     * Zavolá zadanou operaci a vrátí její výsledek.
     * @param int $operation zadaná operace
     * @param int $x         první číslo pro operaci
     * @param int $y         druhé číslo pro operaci
     * @return int|null výsledek operace nebo null, pokud zadaná operace neexistuje
     */
    public function calculate(int $operation, int $x, int $y)
    {
        switch ($operation) {
            case self::ADD:
                return $this->add($x, $y);
            case self::SUBTRACT:
                return $this->subtract($x, $y);
            case self::MULTIPLY:
                return $this->multiply($x, $y);
            case self::DIVIDE:
                return $this->divide($x, $y);
            default:
                return null;
        }
    }

	/**
	 * Sečte daná čísla a vrátí výsledek.
	 * @param int $x první číslo
	 * @param int $y druhé číslo
	 * @return int výsledek po sčítání
	 */
	public function add(int $x, int $y)
	{
		return $x + $y;
	}

	/**
	 * Odečte druhé číslo od prvního a vrátí výsledek.
	 * @param int $x první číslo
	 * @param int $y druhé číslo
	 * @return int výsledek po odčítání
	 */
	public function subtract(int $x, int $y)
	{
		return $x - $y;
	}

	/**
	 * Vynásobí daná čísla a vrátí výsledek.
	 * @param int $x první číslo
	 * @param int $y druhé číslo
	 * @return int výsledek po násobení
	 */
	public function multiply(int $x, int $y)
	{
		return $x * $y;
	}

	/**
	 * Vydělí první číslo druhým bezezbytku a vrátí výsledek.
	 * @param int $x první číslo
	 * @param int $y druhé číslo; nesmí být 0
	 * @return int výsledek po dělení bezezbytku
	 */
	public function divide(int $x, int $y)
	{
		return round($x / $y);
	}
}