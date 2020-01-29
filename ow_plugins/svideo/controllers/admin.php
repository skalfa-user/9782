<?php

class SVIDEO_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    /**
     * Default action
     */
    public function index()
    {
        $language = OW::getLanguage();

        $form = new Form('video-settings');
        $videoField = new Textarea('video');
        $videoField->setRequired();
        $videoField->setLabel($language->text('svideo', 'video_label'));
        $videoField->setValue(OW::getConfig()->getValue('svideo', 'video'));
        $form->addElement($videoField);

        $titleField = new TextField('title');
        $titleField->setRequired();
        $titleField->setLabel($language->text('svideo', 'title_label'));
        $titleField->setValue(OW::getConfig()->getValue('svideo', 'title'));
        $form->addElement($titleField);

        $descField = new Textarea('description');
        $descField->setRequired();
        $descField->setLabel($language->text('svideo', 'description_label'));
        $descField->setValue(OW::getConfig()->getValue('svideo', 'description'));
        $form->addElement($descField);

        $submit = new Submit('save');
        $submit->setValue($language->text('svideo', 'btn_save'));
        $form->addElement($submit);

        $this->addForm($form);

        if (OW::getRequest()->isPost() && $form->isValid($_POST))
        {
            $values = $form->getValues();

            OW::getConfig()->saveConfig('svideo', 'video', $values['video']);
            OW::getConfig()->saveConfig('svideo', 'title', $values['title']);
            OW::getConfig()->saveConfig('svideo', 'description', $values['description']);
            OW::getFeedback()->info($language->text('svideo', 'save_feedback'));
            $this->redirect();
        }

        OW::getDocument()->setHeading($language->text('svideo', 'video_settings_head'));

    }
}
