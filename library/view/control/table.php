<?php

$field = array();
foreach ($this->control()->config->field as $k => $v) {
	if (!$v->active || $v->hidden) continue;
	$field[$k] = clone $v;
}

if (!$field) return;

$count_percent = 0;
$is_search = false;
foreach ($field as $k => $v) {
	if (stripos($v->width, 'px') === false) $count_percent++;
	if ($v->search) $is_search = true;
}

$total = 0;
$w = 100 / $count_percent;
foreach ($field as $k => $v) {
	$field[$k]->width_percent = stripos($v->width, 'px') === false
		? ($v->width ? $v->width : $w)
		: $v->width;
	$total += $field[$k]->width_percent;
}

while ($total > 100 || $total < 98) {
	$dif = (100 - $total) / $count_percent;
	$total = 0;
	foreach ($field as $k => $v) {
		if (stripos($v->width_percent, 'px') !== false) continue;
		$field[$k]->width_percent = $field[$k]->width_percent + $dif;
		$total += $field[$k]->width_percent;
	}
}

foreach ($field as $k => $v) $field[$k]->width_percent = stripos($v->width_percent, 'px') === false
	? floor($v->width_percent).'%'
	: $v->width_percent;

$data = $this->data ? clone $this->data : array();

?>
		<table class="table table-striped table-bordered table-hover c-table<?php echo $this->control()->config->tree ? ' c-table-tree' : '' ?>">
			<thead>
				<tr>
					<?php echo $this->control()->config->table->checkbox ? '<th class="c-table-cb">'.(count($data) ? '<input type="checkbox" />' : '&nbsp;').'</th>' : '' ?>
<?php

$p = clone $this->control()->config->param;
foreach ($field as $k => $v) {
	$class = array();
	if ($v->align != 'left') $class[] = 'text-'.$v->align;
	if ($v->sortable) $class[] = 'c-table-sortable';
?>
					<th<?php echo $class ? ' class="'.implode(' ', $class).'"' : '' ?> style="width:<?php echo $v->width_percent ?>;">
<?php

	if ($v->sortable) {
		$p['orderby'] = $k;
		if ($k == $this->control()->config->param->orderby) {
			$p['orderdir'] = $this->control()->config->param->orderdir == 'asc' ? 'desc' : 'asc';
		}
		else {
			$p['orderdir'] = 'asc';
		}

?>
						<span class="c-table-caret"><?php echo $k == $this->control()->config->param->orderby ? '<span'.($k == $this->control()->config->param->orderby ? (' class="c-table-caret-'.($this->control()->config->param->orderdir == 'asc' ? 'n' : 's').'"') : '').'></span>' : '<span class="c-table-caret-ns-up"></span><span class="c-table-caret-ns-down"></span>' ?></span>
						<a href="<?php echo $this->url($p, 'control') ?>"><?php echo $v->title ?></a>
						  
<?php

	}
	else echo $v->title;

?>
					</th>
<?php

}

?>
				</tr>
<?php

if ($is_search) {
$p = clone $this->control()->config->param;
unset($p['page']);
if (count($p)) foreach ($p as $k => $v) if (stripos($k, 'search_') !== false) unset($p[$k]);

?>
				<tr class="c-table-filter">
					<?php echo $this->control()->config->table->checkbox ? '<td><a href="'.$this->url($p, 'control').'" class="btn btn-mini c-table-filter-clear">X</a></td>' : '' ?>
<?php

	foreach ($field as $k => $v) {

?>
					<td>
<?php

		if ($v->search) echo '<div class="row-fluid">'.$this->render($v->search->script, $v->search).'</div>';
		else echo '&nbsp;';

?>
					</td>
<?php

	}

?>
				</tr>
<?php

}

?>
			</thead>
<?php

if (count($data)) {

?>
			<tbody>
<?php

	foreach ($data as $n => $el) {

?>
				<tr data-id="<?php echo $el->id ?>">
					<?php echo $this->control()->config->table->checkbox ? '<td class="c-table-cb"><input type="checkbox" /></td>' : '' ?>
<?php

		foreach ($field as $k => $v) {
			$class = array();
			if ($k == $this->control()->config->param->orderby) $class[] = 'c-table-sorting';
			if ($v->align != 'left') $class[] = 'text-'.$v->align;

?>
					<td<?php echo $class ? ' class="'.implode(' ', $class).'"' : '' ?>><?php echo $v->script ? $this->render($v->script, array('value' => $el->{$k.'_control'}, 'data' => $el)) : $el->{$k.'_control'} ?></td>
<?php
		}

?>
				</tr>
<?php

	}

?>
			</tbody>
<?php

}
else {

?>
			<tbody>
				<tr><td colspan="<?php echo count($field) ?>"><?php echo $this->translate('control_list_empty') ?></td></tr>
			</tbody>
<?php

}

?>
		</table>
<?php

if (count($data)) {
	$perpage = trim($this->xlist(array(
		'fetch' => array(
			'data' => $this->control()->config->perpage_list
		),
		'view' => array(
			'script' => 'control/perpage'
		)
	)));
	if ($perpage || $this->pager) {

?>
		<div class="row-fluid c-pager-perpage">
			<div class="span6"><?php echo $perpage ?></div>
			<div class="span6"><?php echo $this->pager ?></div>
		</div>
<?php

	}
}