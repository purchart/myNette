<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\CmsManager;

final class HomepagePresenter extends BaseFrontPresenter {

    public function renderDefault(): void {
        $this->template->article = $this->cmsManager->getHomePage();
    }

}