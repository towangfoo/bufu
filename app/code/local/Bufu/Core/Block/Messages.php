<?php
/**
 * Bufu_Core_Block_Messages
 */
class Bufu_Core_Block_Messages extends Mage_Core_Block_Messages
{
    /**
     * Retrieve messages in HTML format grouped by type
     *
     * @param   string $type
     * @return  string
     */
    public function getGroupedHtml()
    {
        $types = array(
            Mage_Core_Model_Message::ERROR,
            Mage_Core_Model_Message::WARNING,
            Mage_Core_Model_Message::NOTICE,
            Mage_Core_Model_Message::SUCCESS
        );
        $html = '';
        $first = true;
        foreach ($types as $type) {
            if ( $messages = $this->getMessages($type) ) {
                if ( !$html ) {
                    $html .= '<ul class="messages">';
                }
                $html .= '<li class="' . $type . '-msg';
                if ($first) {
                    $html .= ' first';
                    $first = false;
                }
                $html .=  '">';
                $html .= '<ul>';

                foreach ( $messages as $message ) {
                    $html.= '<li>';
                    $html.= ($this->_escapeMessageFlag) ? $this->htmlEscape($message->getText()) : $message->getText();
                    $html.= '</li>';
                }
                $html .= '</ul>';
                $html .= '</li>';
            }
        }
        if ( $html) {
            $html .= '</ul>';
        }
        return $html;
    }
}
