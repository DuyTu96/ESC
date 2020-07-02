<?php
function adminer_object() {
    class AdminerSoftware extends Adminer {
        function login($login, $password) {
            $whiteList = array(
                // Write IP addresses which are permitted.
                "xxx.xxx.xxx.xxx",
                "yyy.yyy.yyy.yyy"
            );

            $remoteIP = $_SERVER["REMOTE_ADDR"];
            if(in_array($remoteIP, $whiteList) === true ) {
                return true;
            } else {
                return false;
            }
        }
    }
    return new AdminerSoftware;
}