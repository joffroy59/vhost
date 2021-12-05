<?php

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

class JFormFieldArtContent extends JFormField
{
    protected $type = 'ArtContent';

    protected function getInput()
    {
        // Initialize field attributes.
        $text   = $this->element['text'] ? $this->element['text'] : '';
        $value  = $this->element['value'] ? $this->element['value'] : '';
        
        // get theme name
        $id     = JRequest::getInt('id');
        // Get a table instance.
        $table  = JTable::getInstance("Style", "TemplatesTable");
        // Attempt to load the row.
        $table->load($id);
        $template = $table->template;
        
        $dataFolder = JURI::root(true).'/templates/'. $template .'/data';
        $document = JFactory::getDocument();
        
        // include js, css files to create modal window
        $pathToMoootoolsCoreJs =  JURI::root(true).'/media/system/js/mootools-core.js';
        $document->addScript($pathToMoootoolsCoreJs);
        $pathToMoootoolsMoreJs =  JURI::root(true).'/media/system/js/mootools-more.js';
        $document->addScript($pathToMoootoolsMoreJs);
        $pathToModalJs =  JURI::root(true).'/media/system/js/modal.js';
        $document->addScript($pathToModalJs);
        $pathToModalCss = JURI::root(true).'/media/system/css/modal.css';
        $document->addStyleSheet($pathToModalCss);
        
        $templateFolder = dirname(dirname(__FILE__));

        $content = "if ('undefined' != typeof jQuery) document._artxJQueryBackup = jQuery;";
        // join jquery.js file
        $content .= file_get_contents($templateFolder . '/jquery.js');
        $content .= 'jQuery.noConflict();';
        // join loader.js file
        $content .= file_get_contents($templateFolder . '/data/loader.js');
        $content .= 'if (document._artxJQueryBackup) jQuery = document._artxJQueryBackup;';
        
        file_put_contents($templateFolder . '/modules.js', $content);
        $document->addScript(JURI::root(true).'/templates/'. $template .'/modules.js');
        
        return '<button class="modal btn" type="submit" name="' . $this->name . '" id="' . $this->id . '">'. JText::_($text) . '</button>'
        .'<input type="hidden" id="dataFolder" value="'. $dataFolder .'">'
        .'<input type="hidden" id="themeId" value="'. $id .'">'
        .'<div id="log" style="float:left;width:100%;margin-left:150px"></div>';
    }
}
