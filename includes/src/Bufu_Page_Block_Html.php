<?php
/*
 * Bufu_Page_Block_Html
 */
class Bufu_Page_Block_Html extends Mage_Page_Block_Html {

    /*
     * returns true if a browser notice for IE6 users shall be shown
     * (happens only on first page impression of the session)
     *
     * @return boolean
     */
    public function isDisplayOutdatedBrowserNotice()
    {
        if (!Mage::getSingleton('core/session')->getDisplayedOutdatedBrowserNotice()) {
            Mage::getSingleton('core/session')->setDisplayedOutdatedBrowserNotice(true);
            return true;
        }
        return false;
    }

    /*
     * outdated browser notice
     *
     * @return string
     */
    public function getOutdatedBrowserNotice()
    {
        return "
<!--[if lt IE 7]>
  <div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative; margin-bottom:1em'>
    <div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display=\"none\"; return false;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='schließen'/></a></div>
    <div style='width: 800px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>
      <div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>
      <div style='width: 435px; float: left; font-family: Arial, sans-serif;'>
        <div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>Sie verwenden einen seit Jahren veralteten Browser.</div>
        <div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>Um diese Seite und das Internet insgesamt besser und sicherer nutzen zu können, sollten Sie auf einen modernen Browser umsteigen.</div>
      </div>
      <div style='width: 75px; float: left;'><a href='http://www.firefox.com' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>
      <div style='width: 75px; float: left;'><a href='http://www.browserforthebetter.com/download.html' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-ie8.jpg' style='border: none;' alt='Get Internet Explorer 8'/></a></div>
      <div style='width: 73px; float: left;'><a href='http://www.apple.com/safari/download/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-safari.jpg' style='border: none;' alt='Get Safari 4'/></a></div>
      <div style='float: left;'><a href='http://www.google.com/chrome' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>
    </div>
  </div>
  <![endif]-->
        ";
    }

}