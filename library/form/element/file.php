<?php

class k_form_element_file extends form_element_input {
	public $view_script = 'form/file';
	public $class_delete = '';
	public $multiple = false;
	public $name_filer_length = 20;
	public $uploadifive = false;
	public $path = '';
	public $prefix = '';
	public $url = '';

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->type = 'file';
		if (isset($param['uploadifive'])) {
			if (!($param['uploadifive'] instanceof data)) $param['uploadifive'] = new data(is_array($param['uploadifive']) ? $param['uploadifive'] : array());
			if (!isset($param['uploadifive']->css)) $param['uploadifive']->css = true;
			$this->uploadifive = $param['uploadifive'];
		}
		if (isset($param['name_filer_length'])) $this->name_filer_length = $param['name_filer_length'];
		if (isset($param['multiple'])) $this->multiple = $param['multiple'];
		if (isset($param['prefix'])) $this->prefix = $param['prefix'];
		if (isset($param['path'])) $this->path = $param['path'];
		if (isset($param['url'])) $this->url = $param['url'];
		if (isset($param['class_delete'])) $this->class_delete = $param['class_delete'];
	}

	public function validate($value) {
		if (isset($this->validator['image_size'])) {
			if (!isset($this->validator['image_size']['path'])) $this->validator['image_size']['path'] = $this->path;
		}

		if (@$_POST[$this->name.'_delete']) {
			@unlink($this->path.'/'.$_POST[$this->name.'_delete']);
			$this->set('DELETED');
		}
		else {
			$upload = new validator_upload(new data(array(
				'path' => $this->path,
				'prefix' => $this->prefix,
				'name_filer_length' => $this->name_filer_length
			)));
			$result = $upload->validate($this->name);
			if (!is_array($result)) $value = $result;
			parent::validate($value);
		}
	}

	public function render() {
		if ($this->uploadifive) {
			session::set('uploadifive_'.$this->name, array(
				'path' => $this->path,
				'validator' => $this->validator,
				'name_filer_length' => $this->name_filer_length
			));
			$opt1 = $this->uploadifive->opt ? $this->uploadifive->opt->to_array() : array();
			$opt = array(
				'width' => '160px',
				'fileObjName' => $this->name,
				'dnd' => new Zend\Json\Expr(0),
				'buttonText' => $this->view->translate('form_element_file_uploadifive_button_text'.($this->multiple ? '_multiple' : '')),
				'uploadScript' => $this->view->url(array(
					'controller' => 'x',
					'action' => 'upload'
				)),
				'onUploadComplete' => new Zend\Json\Expr('function(file, text) {
					var t = $(this);
					if (typeof text != "string") text = "";
					if (text.slice(0, 3) == "ok|") {
						var v = text.slice(3);
						var file_data = window.uploadifive_data(v, file.queueItem);
						file_data.queueItem.find(".filename").html(v);
						file_data.queueItem.find(".image img").attr("src", "'.$this->url.'/" + v + "?" + Math.random() * 10000);
						file_data.queueItem.data("file", file_data);
					}
					else if (text.length && file.queueItem) {
						var info = text.replace("|", ", ");
						file.queueItem.find(".fileinfo").html(" - " + info);
						file.queueItem.find(".image").remove();
						var file_1 = file.queueItem.data("file");
						file_1.name = "";
					}
					else {
						file.queueItem.find(".image img").attr("src", "'.$this->url.'/" + file.name + "?" + Math.random() * 10000);
					}
					var filename = file.queueItem.find(".filename").html();
					if (file.queueItem.find(".fileinfo").html().indexOf("Completed") !== -1) file.queueItem.find(".fileinfo").html(filename.length ? " / <a target=\"_blank\" href=\"'.$this->url.'/" + filename + "\">'.$this->view->translate('control_download').'</a>" : "");
					window.uploadifive_update(t.parent().parent());
					'.@$opt1['customUpload'].'
				}'),
				'onSelect' => $this->multiple ? null : new Zend\Json\Expr('function(file) {
					var data = this.data("uploadifive");
					$(this).parent().parent().find("#'.$this->view->escape($this->name).'_delete").remove();
					if (data.queue.count <= 1) return;
					var first = data.queueEl.find(".uploadifive-queue-item:first");
					if (first.length) {
						var file = first.data("file");
						if (file) data.removeQueueItem(file, true);
					}
					'.@$opt1['customSelect'].'
				}'),
				'onCancel' => new Zend\Json\Expr('function() {
					var parent = $(this).parent().parent();
					window.setTimeout(function() {
						'.($this->multiple ? '' : 'parent.append("<input type=\"hidden\" value=\"'.$this->view->escape($this->value).'\" name=\"'.$this->view->escape($this->name).'_delete\" id=\"'.$this->view->escape($this->name).'_delete\" />");').'
						window.uploadifive_update(parent);
					}, 800);
					'.@$opt1['customCancel'].'
				}'),
				'onInit' => new Zend\Json\Expr('function() {
					var t = $(this);
					var parent = t.parent().parent();
					var old = parent.find("input[type=hidden]:first").val();
					parent.find(".e-form-element-file-value").remove();
					if (old) {
						var files = old.split(",");
						for (k in files) {
							if (files[k].length) {
								var data = this.data("uploadifive");
								var file = window.uploadifive_data(files[k]);
								data.addQueueItem(file);
								file.queueItem.find(".progress").hide();
								data.uploadComplete(null, file, false);
							}
						}
					}
					if (parent.find("input[type=hidden]:first").length == 0) t.after("<input type=\"hidden\" name=\"" + t.attr("name") + "\" value=\"\" />");
					window.uploadifive_update(parent);
					'.@$opt1['customInit'].'
				}')
			);
			if ($this->uploadifive->opt) {
				unset($opt1['customSelect']);
				unset($opt1['customUpload']);
				unset($opt1['customInit']);
				unset($opt1['customCancel']);
				$opt = array_merge($opt, $opt1);
			}
			$this->view->js->append('/library/ctl/uploadifive/jquery.uploadifive.js');
			$this->view->js->append_inline(
'if (typeof window.uploadifive_update == "undefined") window.uploadifive_update = function(o) {
	var val = [];
	o.find(".uploadifive-queue-item").each(function() {
		var file = $(this).data("file");
		if (file && (typeof file.skip == "undefined" || !file.skip)) val.push(file.name);
	});
	o.find("input[type=hidden]:first").val(val.join(","));

};
if (typeof window.uploadifive_data == "undefined") window.uploadifive_data = function(name, item) {
	return {
		name: name,
		xhr: { responseText: "" },
		queueItem: item,
		complete: true
	};
};
$("input[type=file][name=\''.$this->name.'\']").uploadifive('.Zend\Json\Json::encode($opt, false, array(
	'enableJsonExprFinder' => true
)).');');
			if ($this->uploadifive->css) $this->view->css->append('/library/ctl/uploadifive/uploadifive.css');
		}
		return parent::render();
	}
}