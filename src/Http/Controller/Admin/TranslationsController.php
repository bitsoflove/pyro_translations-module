<?php namespace Bitsoflove\TranslationsModule\Http\Controller\Admin;

use Bitsoflove\TranslationsModule\Translation\Form\TranslationFormBuilder;
use Bitsoflove\TranslationsModule\Translation\Table\TranslationTableBuilder;
use Anomaly\Streams\Platform\Http\Controller\AdminController;

class TranslationsController extends AdminController
{

    /**
     * Display an index of existing entries.
     *
     * @param TranslationTableBuilder $table
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(TranslationTableBuilder $table)
    {
        return $table->render();
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
