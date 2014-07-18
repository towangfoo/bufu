<?php
/**
 * Review controller
 *
 * overriden to send notification mail, wehen a new review is posted
 */

require_once "Mage/Review/controllers/ProductController.php";

class Bufu_Review_ProductController extends Mage_Review_ProductController
{

    public function postAction()
    {
        $data   = $this->getRequest()->getPost();
        Mage::dispatchEvent('bufu_review_controller_product_post', array('data'=>$data));

        parent::postAction();
    }

}
