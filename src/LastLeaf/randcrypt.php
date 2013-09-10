<?php if(!defined('__DIRECT_REQUEST__')) exit(-1);

class RandCrypt {

	public function hash($str, $cost=7) {
		// generate random salt
    	$chars = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' ;
    	$salt = sprintf('$2a$%02d$', $cost);
    	for($i=0; $i<22; $i++)
    		$salt .= $chars[ mt_rand(0, 63) ];
    	// crypt
    	return crypt($str, $salt);
	}
	
	public function check($str, $res) {
		return $res === crypt($str, $res);
	}

}