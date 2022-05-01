<?php

declare(strict_types=1);

namespace App\Forms;

use Nette;
use Nette\SmartObject;
use Nette\Application\UI\Form;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

final class FormFactory
{
    use SmartObject;

    public function create(): Form
    {
        $form = new Form;
            $form->setRenderer(new BootstrapVerticalRenderer);
        return $form;
    }
}
