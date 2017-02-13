<?php
namespace Bitsoflove\TranslationsModule\Repositories\Modules\Interfaces;

interface TranslationRepositoryInterface
{
    public function getAddonTranslations($addonNamespace, $locale, array $parameters=[]);
}
