<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php

if (count($this->data)) {
	foreach ($this->data as $el) {
		$priority = $el->priority ? $el->priority : $this->priority;
		$lastmod = $el->lastmod ? $el->lastmod : $this->lastmod;

?>	<url>
		<loc><?php echo (stripos($el->href, 'http://') === false ? 'http://'.$_SERVER['HTTP_HOST'] : '').$el->href ?></loc>
<?php echo $lastmod ? '		<lastmod>'.$lastmod.'</lastmod>'."\n" : '' ?><?php echo $priority ? '		<priority>'.$priority.'</priority>'."\n" : '' ?>
	</url>
<?php

	}
}

?>
</urlset>