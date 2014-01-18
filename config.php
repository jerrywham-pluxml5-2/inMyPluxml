<?php
/**
 * Plugin inMyPluxml
 *
 * @package	PLX
 * @version	1.3
 * @date	18/01/2014
 * @author	Cyril MAGUIRE
 **/
 
	if(!defined('PLX_ROOT')) exit; 
	
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);
	
	if(!empty($_POST)) {
		$plxPlugin->setParam('catName', $_POST['catName'], 'cdata');
		$plxPlugin->saveParams();
		header('Location: parametres_plugin.php?p=inMyPluxml');
		exit;
	}
?>

<h2><?php $plxPlugin->lang('L_CONFIG_DESCRIPTION') ?></h2>

<form action="parametres_plugin.php?p=inMyPluxml" method="post">
	<fieldset class="withlabel">
		<p><?php echo $plxPlugin->getLang('L_CAT_NAME') ?></p>
		<?php plxUtils::printInput('catName',plxUtils::strCheck($plxPlugin->getParam('catName')), 'text'); ?>

	</fieldset>
	<br />
	<?php echo plxToken::getTokenPostMethod() ?>
	<input type="submit" name="submit" value="<?php echo $plxPlugin->getLang('L_CONFIG_SAVE') ?>" />
</form>
<p>Glissez le bookmarklet suivant dans votre barre de favoris :</p>
<p>Le bookmarklet -> <a href="javascript:javascript:(function(){var%20url%20=%20location.href;var%20title%20=%20document.title%20||%20url;window.open('<?php echo plxUtils::getRacine();?>core/admin/auth.php?p=article.php&amp;post='%20+%20encodeURIComponent(url)+'&amp;title='%20+%20encodeURIComponent(title)+'&amp;source=bookmarklet','_blank','menubar=no,height=800,width=800,toolbar=no,scrollbars=yes,status=no');})();">Pluxml</a></p>