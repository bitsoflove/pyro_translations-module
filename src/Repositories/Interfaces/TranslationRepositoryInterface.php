<?php
namespace Bitsoflove\TranslationsModule\Repositories\Interfaces;

interface TranslationRepositoryInterface
{
    public function getAddonTranslations($addonNamespace, $locale, array $parameters=[]);
}
