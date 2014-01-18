<?php
/**
 * Plugin inMyPluxml
 *
 * @package	PLX
 * @version	1.2
 * @date	18/01/2014
 * @author	Cyril MAGUIRE
 **/
	/**
	 * Méthode permettant d'encoder une image en base64
	 * 
	 * @param $filename string le chemin vers le fichier image
	 * @param $filetype string l'extension de l'image
	 * @return string
	 * 
	 * @author Cyril MAGUIRE
	 */
	function base64_encode_image ($filename,$filetype,$timeout) {
	    if (is_string($filename) ) {
	        $context = stream_context_create(array('http'=>array('timeout' => $timeout))); // Timeout : time until we stop waiting for the response.
			$imgbinary = @file_get_contents($filename, false, $context, -1, 4000000);
			if ($imgbinary !== false) {
				return 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);
			}else {
				return null;
			}
	    }
	}
	/**
	 * Méthode permettant d'encoder en base64 les images d'un texte
	 * 
	 * @param $text string le texte dont il faut transformer les images
	 * @return string
	 * 
	 * @author Cyril MAGUIRE
	 */
	function encode_img($text) {
		$array_ext = array('jpg','png','gif');
		preg_match_all('!src=[\"\']([^\"\']+)!', $text, $matches,PREG_SET_ORDER);
		foreach ($matches as $matche) {
			$ext = strtolower(substr($matche[1],strrpos($matche[1], '.')+1 ));
			if ($ext == 'jpeg') {$ext = 'jpg';}
			if (in_array($ext, $array_ext)) {
				$i = base64_encode_image($matche[1],$ext,6);
				if ($i != null) {
					$text = str_replace($matche[1], $i, $text);
				}
			}
		}
		return $text;
	}
?>