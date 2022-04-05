<?php

declare(strict_types=1);

namespace app\Presenters;

use Nette\Application\UI\Presenter;
use App\Model\CalculatorManager;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Presenter kalkulacky.
 * @package App\Presenters
 */
final class CalculatorPresenter extends Presenter
{
    const
        FORM_MSG_REQUIRED = 'Tohle pole je povinne.',
        FORM_MSG_RULE = 'Tohle pole ma neplatny format.';

    /** @var int|null vysledek operace nebo null */
    private $result = null;

    /** @var CalculatorManager Instance tridy modelu pro praci s operacemi kalkulacky. */
    private $calculatorManager;

    /**
     * Konstruktor s injektovanym modelem pro praci s operacemi kalkulacky.
     * @param CalculatorManager $calculatorManager automaticky injektovana trida modelu pro praci s operacemi kalkulacky
     */
    public function __construct(CalculatorManager $calculatorManager)
    {
        parent::__construct();
        $this->calculatorManager = $calculatorManager;
    }

    /** vychozi vykreslovaci metoda tohoto presenteru. */
    public function renderDefault()
    {
        // predani vysledku do sablony.
        $this->template->result = $this->result;
    }

    /**
     * Vrati formular kalkulacky
     * @return Form formular kalkulacky
     */
    protected function createComponentCalculatorForm()
    {
        $form = new Form;
        // Ziskame existujici operace kalkulacky a dame je do vyberu operaci.
        $form->addRadioList('operation', 'Operace:', $this->calculatorManager->getOperations())
            ->setDefaultValue(CalculatorManager::ADD)
            ->setRequired(self::FORM_MSG_REQUIRED);
        $form->addText('x', 'Prvni cislo:')
            ->setHtmlType('number')
            ->setDefaultValue(0)
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE);
        $form->addText('y', 'Druhe cislo:')
            ->setHtmlType('number')
            ->setDefaultValue(0)
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE)
            //osetrime deleni nulou
            ->addConditionOn($form['operation'], Form::EQUAL, CalculatorManager::DIVIDE)
            ->addRule(Form::PATTERN, 'Nelze delit nulou.', 'ˆ[ˆ0].*');
        $form->addSubmit('calculate', 'Spocitej vysledek');
        $form->onSuccess[] = [$this, 'calculatorFormSucceeded'];

        return $form;
        
    }

    /** funkce se vykona pri uspesnem odeslani formulare kalkulacky a zpracuje odeslane hodnoty.
     * @param Form $form    formular kalkulacky
     * @param ArrayHash $values odesla hodnoty formulare
     */
    public function calculatorFormSucceeded(Form $form, ArrayHash $values)
    {
        // nechame si vypocitat podel operace a zadanych hodnot.
        $this->result = $this->calculatorManager->calculate($values->operation, $values->x, $values->y);
    }
}