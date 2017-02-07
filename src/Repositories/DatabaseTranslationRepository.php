<?php
namespace Bitsoflove\TranslationsModule\Repositories;

use Anomaly\Streams\Platform\Asset\Asset;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Bitsoflove\TranslationsModule\Repositories\Interfaces\TranslationRepositoryInterface;
use Bitsoflove\TranslationsModule\Translation\Form\TranslationFormBuilder;
use Bitsoflove\TranslationsModule\Translation\Table\TranslationTableBuilder;
use Illuminate\Support\Facades\DB;

class DatabaseTranslationRepository implements TranslationRepositoryInterface
{


    /**
     Geen streams nodig,
     maar altijd interessant om models te gebruiken



     Mogelijk moet er toch een stream gemaakt worden,
     om optimaal van de translations te kunnen genieten
        -> Hoezo ? Waar schieten gewone models te kort ?
        
        => Er is geen dimsav/translatable package (lijkt inline gekopieerd te zijn in EntryModel)
        => En we willen geen conflicten met bestaande code
        => Dus toch gewoon een stream maken!
        => Bij het updaten van een translation entry, kan dan in principe dezelfde API flow gevolgd worden
           Als bij het opslaan van een gewone stream entry. (Echter dan wel: createOrUpdate ipv update)
     */


    public function getAddonTranslations($addonNamespace, $locale, array $parameters=[]) {

        $query = $this->getAddonTranslationsQuery($addonNamespace, $locale);

        throw new \Exception("Unimplemented: DatabaseTranslationRepository@getAddonTranslations");
    }

    private function getAddonTranslationsQuery($addonNamespace, $locale)
    {

        $appRef = env('APPLICATION_REFERENCE');
        $translationsTable = '' . $appRef . '_translations';
        $translationsTranslationsTable = '' . $appRef . '_translations_translations';

        $query = "SELECT `key`, `value`, `locale`  
                  FROM $translationsTable
                  INNER JOIN $translationsTranslationsTable
                  
                  ON $translationsTable.id = $translationsTranslationsTable.translation_id
                  
                  WHERE `key` LIKE '$addonNamespace::%' AND locale = '$locale'
                  ";

        echo($query);

        $res = DB::select(DB::raw($query));
        dd($res);

    }
}
