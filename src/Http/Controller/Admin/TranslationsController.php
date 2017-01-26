<?php
namespace Bitsoflove\TranslationsModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Asset\Asset;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Bitsoflove\TranslationsModule\Translation\Form\TranslationFormBuilder;
use Bitsoflove\TranslationsModule\Translation\Table\TranslationTableBuilder;

class TranslationsController extends AdminController
{

    /**
     * Display an index of existing entries.
     *
     * @param TranslationTableBuilder $table
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $asset = app(Asset::class);

        // append select2 css
        //$asset->add('theme.css', 'module::css/select2.css', ['parse']);

        //$asset->add('theme.js', 'module::js/index.js', ['parse']);
        return view('module::admin/translations');
    }

    /**
     * Create a new entry.
     *
     * @param TranslationFormBuilder $form
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(TranslationFormBuilder $form)
    {
        return $form->render();
    }

    /**
     * Edit an existing entry.
     *
     * @param TranslationFormBuilder $form
     * @param        $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(TranslationFormBuilder $form, $id)
    {
        return $form->render($id);
    }
}
