<?php

class Dull_Uploader_Block_Uploader extends Mage_Adminhtml_Block_Media_Uploader
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('noflashuploader/uploader.phtml');
    }

}
