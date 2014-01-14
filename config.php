<?php
/**
 * Plugin inMyPluxml
 *
 * @package	PLX
 * @version	1.1
 * @date	14/12/2014
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