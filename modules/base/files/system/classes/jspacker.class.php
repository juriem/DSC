<?php
namespace modules\base\files\system\classes; 
/**
 * 
 * Wrappper for JS Packer 
 * @author Juri Em
 *
 */

final class JSPacker {
	public function pack($script){
		require_once('jspacker'.DS.'class.javascriptpacker.php'); 
		$packer = new JavaScriptPacker($script); 
		return $packer->pack($script); 
	}
}