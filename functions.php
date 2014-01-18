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
	function base64_encode_img ($filename,$filetype,$timeout) {
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
				$i = base64_encode_img($matche[1],$ext,6);
				if ($i != null) {
					$text = str_replace($matche[1], $i, $text);
				}
			}
		}
		return $text;
	}
	/**
	 * Méthode permettant de récupérer le contenu d'une image
	 * 
	 * @param $filename string le chemin vers le fichier image
	 * @param $timeout integer le temps de traitement maximal du script
	 * @return string
	 * 
	 * @author Cyril MAGUIRE
	 */
	function read_img ($filename,$timeout) {
	    if (is_string($filename) ) {
	        $context = stream_context_create(array('http'=>array('timeout' => $timeout))); // Timeout : time until we stop waiting for the response.
			$imgbinary = @file_get_contents($filename, false, $context, -1, 4000000);
			if ($imgbinary !== false) {
				return $imgbinary;
			}else {
				return null;
			}
	    }
	}
	/**
	 * Méthode permettant de lire une image encodée en base64
	 * 
	 * @param $filename string le chemin vers le fichier image
	 * @return string
	 * 
	 * @author Cyril MAGUIRE
	 */
	function read_base64_img ($filename) {
	    if (is_string($filename) ) {
	        $imgbinary = preg_replace('!data:image\/[a-z]{3};base64,!', '', $filename);
	        $imgbinary = @base64_decode($imgbinary);
			if ($imgbinary !== false) {
				return $imgbinary;
			}else {
				return null;
			}
	    }
	}
	/**
	 * Méthode permettant de créer une copie locale des images d'un texte
	 * 
	 * @param $text string le texte dont il faut transformer les images
	 * @param $dir string le dossier dans lequel on enregistre les images
	 * @return string
	 * 
	 * @author Cyril MAGUIRE
	 */
	function rec_img($text,$dir) {
		global $plxAdmin;
		$array_ext = array('jpg','png','gif');
		preg_match_all('!src=[\"\']([^\"\']+)!', $text, $matches,PREG_SET_ORDER);
		if(!empty($matches)) {
			if($plxAdmin->aConf['userfolders'] AND $_SESSION['profil']==PROFIL_WRITER) {
				if (!is_dir(PLX_ROOT.$plxAdmin->aConf['images'].$_SESSION['user'].'/'.$dir) ) {
					@mkdir(PLX_ROOT.$plxAdmin->aConf['images'].$_SESSION['user'].'/'.$dir);
					$url = $plxAdmin->aConf['images'].$_SESSION['user'].'/'.$dir.'/';
					$dir = PLX_ROOT.$plxAdmin->aConf['images'].$_SESSION['user'].'/'.$dir.'/';
				}
			} else {
				if (!is_dir(PLX_ROOT.$plxAdmin->aConf['images'].$dir) ) {
					@mkdir(PLX_ROOT.$plxAdmin->aConf['images'].$dir);
					$url = $plxAdmin->aConf['images'].$dir.'/';
					$dir = PLX_ROOT.$plxAdmin->aConf['images'].$dir.'/';
				}
			}
		}
		$i=0;
		foreach ($matches as $matche) {
			if (substr($matche[1],0,10) == 'data:image') {
				$ext = substr($matche[1],11,3);
				$data = read_base64_img($matche[1]);
			} else {
				$ext = strtolower(substr($matche[1],strrpos($matche[1], '.')+1 ));
				if (in_array($ext, $array_ext)) {
					$data = read_img($matche[1],6);
				} else {
					$data = null;
				}
			}
			if ($data != null) {
				$img_name = time().'_'.$i.'.'.$ext;
				file_put_contents($dir.$img_name, $data);
				$text = str_replace($matche[1], $url.$img_name, $text);
			}
			$i++;
		}
		return $text;
	}
?>