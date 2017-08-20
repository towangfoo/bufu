<?php
/**
 * Bufu_Newsletter_SubscriberController
 */
class Bufu_Newsletter_SubscriberController extends Mage_Core_Controller_Front_Action
{

    /**
      * New subscription action
      */
    public function newAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session   = Mage::getSingleton('core/session');
            $email     = (string) $this->getRequest()->getPost('email');

            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address'));
                }

                // insert to buschfunk
                $result = $this->orderBuFuNewsletterByRequest($this->getRequest()->getPost());
                if (true === $result) {
                    $session->addSuccess($this->__('Thank you for your subscription'));
                }
                else {
                    $session->addError($this->__('There was a problem with the newsletter subscription'));
                }
            }
            catch (Mage_Core_Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
            }
            catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription'));
            }
        }
        // go back to where we were
        $this->_redirectReferer();
    }

    /**
     * subscribe to BuFu Newsletter via HTTP request
     *
     * @param array $values
     * @return boolean $success | null if no changes
     * @see documentation on buschfunk/tracker/cimprtGundi class for details and settings
     */
    private function orderBuFuNewsletterByRequest($values)
    {
        $remoteAction       = "http://www.buschfunk.com/tracker/nlImprt.php";
        $connectionSalt     = "hGDS73kSDks92ksd2JKLskds4";
        $requestMethod      = "POST";
        $interest           = "News, Gerhard Gundermann";

        $email  = $values['email'];
        $name   = ""; // name left out for now

        // realm
        $realm = md5($name . ":" . $email . ":" . $interest . ':' . $connectionSalt);

        /* debug: show get uri
        $url = $remoteAction.'?name='.base64_encode($name).'&email='.base64_encode($email).
            '&interest='.base64_encode($interest).'&realm='.base64_encode($realm);
        var_dump($url); exit();
        */

        // fire up http client
        $client = new Zend_Http_Client($remoteAction, array(
            'maxredirects' => 0,
            'timeout'      => 30
        ));
        if ('POST' === $requestMethod) {
            $client->setMethod(Zend_Http_Client::POST);
            $client->setParameterPost(array(
                'name'     => base64_encode($name),
                'email'    => base64_encode($email),
                'interest' => base64_encode($interest),
                'realm'    => base64_encode($realm)
            ));
        }
        else {
            $client->setMethod(Zend_Http_Client::GET);
            $client->setParameterGet(array(
                'name'     => base64_encode($name),
                'email'    => base64_encode($email),
                'interest' => base64_encode($interest),
                'realm'    => base64_encode($realm)
            ));
        }

        # shoot request
        $response = $client->request();
        $statusCode = $response->getStatus();

        if (200 === $statusCode) {
            return true;
        }
        elseif (202 === $statusCode) {
            return null;
        }
        else {
            return false;
        }
    }

}

?>