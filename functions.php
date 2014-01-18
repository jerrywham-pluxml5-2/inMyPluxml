<?php
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

	function encode_img($text) {
		preg_match_all('!src=[\"\']([^\"\']+)!', $text, $matches,PREG_SET_ORDER);
		foreach ($matches as $matche) {
			$ext = strtolower(substr($matche[1],strrpos($matche[1], '.')+1 ));
			if ($ext == 'jpeg') {$ext = 'jpg';}
			$i = base64_encode_image($matche[1],$ext,3);
			if ($i != null) {
				$text = str_replace($matche[1], $i, $text);
			}
		}
		return $text;
	}
?>