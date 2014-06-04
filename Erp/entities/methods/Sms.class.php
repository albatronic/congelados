<?php

/**
 * Description of Sms
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Sms extends SmsEntity {

    public function __toString() {
        return $this->getIDSms();
    }

}

?>
