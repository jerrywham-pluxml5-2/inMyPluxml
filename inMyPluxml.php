<?php
/**
 * Plugin inMyPluxml
 *
 * @package	PLX
 * @version	1.0
 * @date	12/12/2014
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
		
		# Déclarations des hooks		
		$this->addHook('AdminArticleInitData', 'AdminArticleInitData');		
		$this->addHook('AdminArticleContent', 'AdminArticleContent');		
		$this->addHook('AdminArticlePostData', 'AdminArticlePostData');		
		$this->addHook('AdminArticleParseData', 'AdminArticleParseData');		
		$this->addHook('AdminAuthPrepend', 'AdminAuthPrepend');		
		$this->addHook('AdminAuthTop', 'AdminAuthTop');		
	}

	/**
	 * Méthode qui initialise les variables d'un article
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/	
	public function AdminArticleInitData() {
		
		$string = '
		if (isset($_GET[\'amp;source\']) && $_GET[\'amp;source\'] == \'bookmarklet\') {

			$options = array(\'http\' => array(\'user_agent\' => \'pluxml\'));
	        $context = stream_context_create($options);
	        $json = file_get_contents(plxUtils::getRacine(). \'plugins/bookmark/3rdparty/makefulltextfeed.php?url=\'.urlencode(trim($_GET[\'post\'])).\'&max=5&links=preserve&exc=&format=json&submit=Create+Feed\', false, $context);
	        $content = json_decode($json, true);
	        $title = $content[\'rss\'][\'channel\'][\'item\'][\'title\'];
	        $body = $content[\'rss\'][\'channel\'][\'item\'][\'description\'];

			# Alimentation des variables
			$artId = \'0000\';
			$author = $_SESSION[\'user\'];
			$catId = array(\'draft\');
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
				$get_p = parse_url(urldecode($_GET[\'p\']));
				if (strpos($get_p, \'post=\') !== false) {
					$post = explode(\'&post=\', $get_p[\'path\']);
					$get_p[\'path\'] = $post[0];
					$source = \'bookmarklet\';
					$post = str_replace(\'&source=bookmarklet\', \'\', $post[1]);
				} else {
					$post = $source = \'\';
				}
				$error = (!$get_p OR (isset($get_p[\'host\']) AND $racine[\'host\']!=$get_p[\'host\']));
				if(!$error AND !empty($get_p[\'path\']) AND file_exists(PLX_ROOT.\'core/admin/\'.basename($get_p[\'path\'])) ) {
					# filtrage des parametres de l\'url
					$query=\'\';
					if(isset($get_p[\'query\']) ) {
						$query=strtok($get_p[\'query\'],\'=\');
						$query=($query[0]!=\'d\'?\'?\'.$get_p[\'query\']:\'\');
					}
					# url de redirection
					$redirect=$get_p[\'path\'].$query;
				}
				if (!empty($source)) {
					$redirect .= \'?post=\'.urlencode($post).\'&source=bookmarklet\';
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
			if(!empty($_GET[\'post\']) && $_GET[\'source\'] == \'bookmarklet\') {
				$redirect .=\'&post=\'.urlencode($_GET[\'post\']).\'&source=bookmarklet\';
			}
		';
		echo "<?php".$string."?>";
	}

}
?>