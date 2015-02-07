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
$dragurl = '';
if ($this->control()->config->drag) {
	$p = clone $this->control()->config->param;
	$p['action'] = 'drag';
	$dragurl = $this->url($p, 'control');
}

foreach ($field as $k => $v) $field[$k]->width_percent = stripos($v->width_percent, 'px') === false
	? floor($v->width_percent).'%'
	: $v->width_percent;

$data = $this->data ? clone $this->data : array();

?>
		<table class="table table-striped table-bordered table-hover c-table<?php echo $this->control()->config->tree ? ' c-table-tree' : '' ?><?php echo count($data) && $this->control()->config->drag ? ' c-table-drag' : '' ?>"<?php echo count($data) && $this->control()->config->drag ? ' data-dragurl="'.$dragurl.'"' : '' ?>>
			<thead>
				<tr>
					<?php echo $this->control()->config->table->number ? '<td>'.$this->translate('control_list_number').'</td>' : '' ?>
					<?php echo $this->control()->config->table->checkbox && (count($data) || $is_search) ? '<th class="c-table-cb">'.(count($data) ? '<input type="checkbox" />' : '<div style="width:13px;">&nbsp;</div>').'</th>' : '' ?>
<?php

$p = clone $this->control()->config->param;
foreach ($field as $k => $v) {
	$class = array();
	if ($v->align != 'left') $class[] = 'text-'.$v->align;
	if ($v->sortable) $class[] = 'c-table-sortable';
?>
					<th<?php /*echo !count($data) && $is_search ? ' colspan="'.($is_search + 1).'"' : ''*/ ?><?php echo $class ? ' class="'.implode(' ', $class).'"' : '' ?> style="width:<?php echo $v->width_percent ?>;">
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
					<?php echo $this->control()->config->drag && count($data) ? '<th>&nbsp;</th>' : '' ?>
				</tr>
<?php

if ($is_search) {
	$p = clone $this->control()->config->param;
	unset($p['page']);
	if (count($p)) foreach ($p as $k => $v) if (stripos($k, 'search_') !== false) unset($p[$k]);

?>
				<tr class="c-table-filter">
					<?php echo $this->control()->config->table->number ? '<td></td>' : '' ?>
					<?php echo $this->control()->config->table->checkbox ? '<td><a href="'.$this->url($p, 'control').'" class="glyphicon glyphicon-remove c-table-filter-clear"></a></td>' : '' ?>
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
					<?php echo $this->control()->config->drag && count($data) ? '<td>&nbsp;</td>' : '' ?>
				</tr>
<?php

}

?>
			</thead>
<?php

if (count($data)) {
	if ($this->control()->config->drag) $this->messify->append('js', '/'.DIR_KERNEL.'/js/jquery/jquery.tablednd.js');

?>
			<tbody>
<?php

	foreach ($data as $n => $el) {

?>
				<tr data-id="<?php echo $el->id ?>">
					<?php echo $this->control()->config->table->number ? '<td class="c-table-number">'.((($this->control()->config->param->page - 1) * $this->control()->config->param->perpage) + ($n + 1)).'</td>' : '' ?>
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
					<?php echo $this->control()->config->drag ? '<td><div class="c-drag"><span class="glyphicon glyphicon-resize-vertical"></span></div></td>' : '' ?>
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
				<tr><td colspan="<?php echo count($field) + $is_search ?>"><?php echo $this->translate('control_list_empty') ?></td></tr>
			</tbody>
<?php

}

?>
		</table>
<?php

if (count($data)) {
	$perpage = $this->control()->config->perpage_show ? trim($this->xlist(array(
		'fetch' => array(
			'data' => $this->control()->config->perpage_list
		),
		'view' => array(
			'script' => 'control/perpage'
		)
	))) : '';
	if ($perpage || $this->pager) {

?>
		<div class="container-fluid c-pager-perpage"><div class="row">
			<div class="col-sm-6"><?php echo $perpage ?></div>
			<div class="col-sm-6"><?php echo $this->pager ?></div>
		</div></div>
<?php

	}
}