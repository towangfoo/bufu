<?php
/*
 * Bufu_Newsletter_Block_Subscribe
 */
class Bufu_Newsletter_Block_Subscribe extends Mage_Newsletter_Block_Subscribe
{

    /**
     * return action url for subscription
     *
     * @return string $url
     */
    public function getSubscribeAction()
    {
        return $this->getUrl('bufu_newsletter/subscriber/new');
    }

}
?>