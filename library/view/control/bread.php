<?php

if (count($this->data) > 1) {

?>
<div class="breadcrumb_frame"><ol class="breadcrumb">
<?php

	$is_inner_cnt = 0;
	foreach ($this->data as $n => $el) {
		if ($el->route == 'control' && $el->is_inner) $is_inner_cnt++;
	}
	if ($is_inner_cnt > 2) {
		$cid_array = array_merge(array_fill(0, $is_inner_cnt - 2, ''), array(array('cid'), array('pid', 'cid')));
	}
	else if ($is_inner_cnt == 2) {
		$cid_array = array(array('cid'), array('pid', 'cid'));
	}
	else if ($is_inner_cnt == 1) {
		$cid_array = array(array('cid'));
	}
	else $cid_array = array();
	foreach ($this->data as $n => $el) {
		$title = $el->title;
		if ($el->route == 'control') {
			$title_1 = '';
			if (($this->control()->config->param->cid && $cid_array) || $this->control()->config->param->oid) {
				$pid = $this->control()->config->param->pid ? $this->control()->config->param->pid : ($this->control()->config->param->cid ? $this->control()->config->param->cid : $this->control()->config->param->oid);
				$prev = @$this->data[$n - 1];
				if ($prev) {
					$view = new view();
					ob_start();
					$view->control(array(
						'controller' => $prev->controller,
						'action' => 'gt',
						'param' => array(
							'id' => $pid
						)
					))->run();
					$title_1 = ob_get_clean();
				}
			}
			if ($el->is_inner && $cid_array) {
				$key = array_shift($cid_array);
				if ($key) {
					$p = array();
					foreach ($key as $el_1) {
						if (!$this->control()->config->param->$el_1) continue;
						$p[$el_1] = $this->control()->config->param->$el_1;
					}
					$el->param = $p;
				}
			}
			if (!$cid_array && $this->control()->config->param->oid && isset($el->param->oid)) {
				$el->param = array(
					'oid' => $this->control()->config->param->oid
				);
			}
			if ($title_1) $title .= ': <small>'.$title_1.'</small>';
		}
		$h = $el->href;
		if ($h == '/') $h = '';
		if ($h) {

?>
	<li><a href="<?php echo $h ?>"><?php echo $title ?></a></li>
<?php

		}
		else {

?>
	<li class="active"><?php echo $title ?></li>
<?php

		}
	}

?>
	</ol></div>
<?php

}