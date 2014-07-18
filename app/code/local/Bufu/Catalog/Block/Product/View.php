<?php
/*
 * Bufu_Catalog_Block_Product_View
 */
class Bufu_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_View {

    /**
     * return html formatted tracklist
     *
     * @return string | false
     */
    public function getTracklistHtml()
    {
        $rawData = trim($this->getProduct()->getBufuTracklist());
        if (strlen($rawData) < 1) return false;

        $lines = explode("\n", $rawData);
        $inner = '';
        $cntList = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            if (substr($line,0,1) == "#") {
                $line = trim(substr($line,1));
                if ($cntList > 0) {
                    $inner .= "</ol>";
                }
                $inner .= '<span class="label">'.$line.'</span>'."\n";
                if ($cntList > 0) {
                    $cntList = 0;
                }
            }
            elseif (strlen($line)>0) {
                if ($cntList == 0) {
                    $inner .= '<ol>';
                }
                $inner .= '<li>'.$line.'</li>'."\n";
                $cntList ++;
            }
        }

        return $inner."</ol>";
    }

}
?>