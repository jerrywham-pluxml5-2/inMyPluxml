<?php
/**
 * Plugin inMyPluxml
 *
 * @package	PLX
 * @version	1.1
 * @date	14/01/2014
 * @author	Cyril MAGUIRE
 **/
class inMyPluxml extends plxPlugin {

	/**
	 * Constructeur de la classe inMyPluxml
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);

		# Déclarations des hooks		
		$this->addHook('AdminArticlePrepend', 'AdminArticlePrepend');		
		$this->addHook('AdminArticleInitData', 'AdminArticleInitData');		
		$this->addHook('AdminArticleContent', 'AdminArticleContent');		
		$this->addHook('AdminArticlePostData', 'AdminArticlePostData');		
		$this->addHook('AdminArticleParseData', 'AdminArticleParseData');		
		$this->addHook('AdminAuthPrepend', 'AdminAuthPrepend');		
		$this->addHook('AdminAuthTop', 'AdminAuthTop');		
	}
	/**
	 * Méthode qui préconfigure le plugin
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function onActivate() {
		#Paramètres par défaut
		if(!is_file($this->plug['parameters.xml'])) {
			$this->setParam('catName', $this->getLang('L_CAT'), 'cdata');
			$this->saveParams();
		}
	}
	/**
	 * Méthode qui crée la catégorie par défaut si elle n'existe pas
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function AdminArticlePrepend() {
		$string = '
	        foreach($plxAdmin->aCats as $cat_id => $cat_name) {
	        	if ($cat_name[\'name\'] == \''.$this->getParam('catName').'\') {
	        		$alreadyExists = true;
	        		$catId = $cat_id;
	        		break;
	        	} else {
	        		$alreadyExists = false;
	        	}
	        }
	        if ($alreadyExists === false) {
	        	$_POST[\'new_category\'] = true;
	        	$_POST[\'new_catname\'] = \''.$this->getParam('catName').'\';
	        }
	        # Ajout d\'une catégorie
			if(isset($_POST[\'new_category\'])) {
				# Ajout de la nouvelle catégorie
				$plxAdmin->editCategories($_POST);
				# On recharge la nouvelle liste
				$plxAdmin->getCategories(path(\'XMLFILE_CATEGORIES\'));
		        unset($_POST[\'new_category\']);
		        unset($_POST[\'new_catname\']);
			}
		';
		echo "<?php ".$string."?>";
	}
	/**
	 * Méthode qui initialise les variables d'un article
	 *
	 *  @return	stdio
	 * @author	Cyril MAGUIRE
	 **/	
	public function AdminArticleInitData() {
		
		$string = '
		if (isset($_GET[\'amp;source\']) && $_GET[\'amp;source\'] == \'bookmarklet\') {
			include(PLX_PLUGINS.\'inMyPluxml/functions.php\');
			$options = array(\'http\' => array(\'user_agent\' => \'poche\'));
	        $context = stream_context_create($options);
	        $json = file_get_contents(plxUtils::getRacine(). \'plugins/inMyPluxml/3rdparty/makefulltextfeed.php?url=\'.urlencode(trim($_GET[\'post\'])).\'&max=5&links=preserve&exc=&format=json&submit=Create+Feed\', false, $context);
	        $content = json_decode($json, true);
	        $title = $content[\'rss\'][\'channel\'][\'item\'][\'title\'];
	        $body = $content[\'rss\'][\'channel\'][\'item\'][\'description\'];
	        $body = encode_img($body);

			# Alimentation des variables
			$artId = \'0000\';
			$author = $_SESSION[\'user\'];
			foreach($plxAdmin->aCats as $cat_id => $cat_name) {
	        	if ($cat_name[\'name\'] == \''.$this->getParam('catName').'\') {
	        		if (isset($catId) && is_array($catId)) {
	        			$catId[] = $cat_id;
	        		} else {
	        			$catId = array();
	        			$catId[] = $cat_id;
	        		}
	        		break;
	        	}
	        }
			$date = array (\'year\' => date(\'Y\'),\'month\' => date(\'m\'),\'day\' => date(\'d\'),\'time\' => date(\'H:i\'));
			$chapo = \'<p>Site d\\\'origine : <a href="\'.trim($_GET[\'post\']).\'">\'.trim($_GET[\'post\']).\'</a></p>\';
			$content =  $body;
			$tags = \'\';
			$url = \'\';
			$allow_com = $plxAdmin->aConf[\'allow_com\'];
			$template = \'article.php\';
			$meta_description = \'\';
			$meta_keywords = \'\';
			$title_htmltag = \'\';
			if(empty($title)) {
				$title = trim($_GET[\'amp;title\']);
			}
			$_SESSION[\'bookmarklet\'] = true;
		}
		';
		echo "<?php ".$string."?>";
	}

	/**
	 * Méthode qui ajoute la provenance des données afin de pouvoir fermer automatiquement la fenêtre après l'enregistrement
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function AdminArticleContent() {
		$string = '
		if (isset($_GET[\'amp;source\']) && $_GET[\'amp;source\'] == \'bookmarklet\') {
			echo \'<input type="hidden" name="source" value="bookmarklet" />\';
		}
		if (isset($_POST[\'source\']) && $_POST[\'source\'] == \'bookmarklet\') {
			echo \'<input type="hidden" name="source" value="bookmarklet" />\';
		}
		';
		echo "<?php ".$string."?>";
	}
	/**
	 * Méthode qui ferme automatiquement la fenêtre après l'enregistrement
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function AdminArticlePostData() {
		$string = '
			// If we are called from the bookmarklet, we must close the popup:
	        if (isset($_SESSION[\'bookmarklet\'])) { unset($_SESSION[\'bookmarklet\']); echo \'<script>self.close();</script>\'; exit(); }
	        if (isset($_POST[\'source\']) && $_POST[\'source\']==\'bookmarklet\') { echo \'<script>self.close();</script>\'; exit(); }
	    ';
		echo "<?php ".$string."?>";
	}
	/**
	 * Méthode qui ferme automatiquement la fenêtre après l'enregistrement
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function AdminArticleParseData() {
		$string = '
			// If we are called from the bookmarklet, we must close the popup:
	        if (isset($_SESSION[\'bookmarklet\'])) { unset($_SESSION[\'bookmarklet\']); echo \'<script>self.close();</script>\'; exit(); }
	        if (isset($_POST[\'source\']) && $_POST[\'source\']==\'bookmarklet\') { echo \'<script>self.close();</script>\'; exit(); }
	    ';
		echo "<?php ".$string."?>";
	}
	/**
	 * Méthode qui transmet l'url de la page à sauvegarder lors de la connexion à pluxml
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function AdminAuthPrepend() {
		$string = '
		# Initialisation variable erreur
		$error = \'\';
		$msg = \'\';
		# Authentification
		if(!empty($_POST[\'login\']) AND !empty($_POST[\'password\'])) {
			# Control et filtrage du parametre $_GET[\'p\']
			$redirect=$plxAdmin->aConf[\'racine\'].\'core/admin/\';
			if(!empty($_GET[\'p\'])) {
				$racine = parse_url($plxAdmin->aConf[\'racine\']);
				$G = urldecode($_GET[\'p\']);
				if (strpos($G, \'post=\') !== false) {
					$post = explode(\'&post=\', $G);
					$get_p = $post[0];
					$source = \'bookmarklet\';
					$title = explode(\'&title=\', $post[1]);
					$post = str_replace(\'&title=\'.$title[1], \'\', $post[1]);
					$title = str_replace(\'&source=bookmarklet\', \'\', $title[1]);
				} else {
					$get_p = $post = $title = $source = \'\';
				}
				if (!empty($source)) {
					$redirect .= $get_p.\'?post=\'.urlencode($post).\'&title=\'.urlencode($title).\'&source=bookmarklet\';
				}
			}
			$connected = false;
			foreach($plxAdmin->aUsers as $userid => $user) {
				if ($_POST[\'login\']==$user[\'login\'] AND sha1($user[\'salt\'].md5($_POST[\'password\']))==$user[\'password\'] AND $user[\'active\'] AND !$user[\'delete\']) {
					$_SESSION[\'user\'] = $userid;
					$_SESSION[\'profil\'] = $user[\'profil\'];
					$_SESSION[\'hash\'] = plxUtils::charAleatoire(10);
					$_SESSION[\'domain\'] = $session_domain;
					$_SESSION[\'lang\'] = $user[\'lang\'];
					$connected = true;
				}
			}
			if($connected) {
				header(\'Location: \'.htmlentities($redirect));
				exit;
			} else {
				$msg = L_ERR_WRONG_PASSWORD;
				$error = \'error\';
			}
		plxUtils::cleanHeaders();
		exit();
		}
		';
		echo "<?php ".$string."?>";
	}
	/**
	 * Méthode qui ajoute l'url à sauvegarder dans le formulaire d'authentification
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function AdminAuthTop() {
		$string = '
			if(!empty($_GET[\'post\']) && !empty($_GET[\'title\']) && $_GET[\'source\'] == \'bookmarklet\') {
				$redirect .=\'&post=\'.urlencode($_GET[\'post\']).\'&title=\'.urlencode($_GET[\'title\']).\'&source=bookmarklet\';
			}
		';
		echo "<?php".$string."?>";
	}

}
?>