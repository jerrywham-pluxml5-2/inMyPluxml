<?php if(!defined('PLX_ROOT')) exit;?>

<p>Glissez le bookmarklet suivant dans votre barre de favoris :</p>
<p>Le bookmarklet -> <a href="javascript:javascript:(function(){var%20url%20=%20location.href;var%20title%20=%20document.title%20||%20url;window.open('<?php echo plxUtils::getRacine();?>core/admin/auth.php?p=article.php&amp;post='%20+%20encodeURIComponent(url)+'&amp;title='%20+%20encodeURIComponent(title)+'&amp;source=bookmarklet','_blank','menubar=no,height=800,width=800,toolbar=no,scrollbars=yes,status=no');})();">Pluxml</a></p>