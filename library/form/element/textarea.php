<?php

class k_form_element_textarea extends form_element {
	public $cols = null;
	public $rows = 10;
	public $view_script = 'form/textarea';

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (isset($param['ckeditor'])) {
			if (!$param['ckeditor'] instanceof data) $param['ckeditor'] = new data();
			if (!isset($param['ckeditor']->opt)) $param['ckeditor']->opt = new data();
			if (!isset($param['ckeditor']->set)) $param['ckeditor']->set = new data();
			if (!isset($param['ckeditor']->style)) $param['ckeditor']->style = 'full';
			if (!isset($param['ckeditor']->class)) $param['ckeditor']->class = '';
			$param['ckeditor']->set = array(
				'def' => array(
					'width' => '100%',
					'allowedContent' => true
				),
				'full' => array(
					'toolbar' => array(
						array( 'name' => 'basicstyles', 'items' => array( 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ) ),
						array( 'name' => 'styles', 'items' => array( 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ) ),
						array( 'name' => 'paragraph', 'items' => array( 'NumberedList', 'BulletedList', 'Outdent', 'Indent' ) ),
						array( 'name' => 'links', 'items' => array( 'Link', 'Unlink', 'Anchor' ) ),
						array( 'name' => 'insert', 'items' => array( 'Image', 'Flash' ) ),
						'/',
						array( 'name' => 'clipboard', 'items' => array( 'PasteText', 'PasteFromWord' ) ),
						array( 'name' => 'styles', 'items' => array( 'Format', 'Font', 'FontSize' ) ),
						array( 'name' => 'colors1', 'items' => array( 'TextColor', 'BGColor' ) ),
						array( 'name' => 'colors2', 'items' => array( 'Table' ) ),
						array( 'name' => 'tools1', 'items' => array( 'PageBreak' ) ),
						array( 'name' => 'tools2', 'items' => array( 'Maximize' ) ),
						array( 'name' => 'tools3', 'items' => array( 'Source' ) )
					),
					'plugins' => 'dialogadvtab,basicstyles,colorbutton,resize,toolbar,elementspath,list,indent,enterkey,filebrowser,flash,font,format,htmlwriter,wysiwygarea,image,justify,link,liststyle,maximize,pagebreak,pastetext,pastefromword,removeformat,sourcearea,table,tabletools'
				),
				'small' => array()
			);
			$param['ckeditor']->opt = $param['ckeditor']->set->def;
			$param['ckeditor']->opt = $param['ckeditor']->set->{$param['ckeditor']->style};
			$this->ckeditor = $param['ckeditor'];
		}
		if (isset($param['cols'])) $this->cols = $param['cols'];
		if (isset($param['rows'])) $this->rows = $param['rows'];
	}

	public function render() {
		$res = parent::render();
		if (@$this->ckeditor) {
			function to_array(&$obj) {
				if ($obj instanceof data) $obj = $obj->to_array();
				if (is_array($obj)) foreach ($obj as &$el) to_array($el);
			}
			$opt = clone $this->ckeditor->opt;
			to_array($opt);

			$this->view->js->append('/kernel/ctl/ckeditor/ckeditor.js');
			if (!class_exists('Zend\Json\Encoder')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Encoder.php';
			if (!class_exists('Zend\Json\Json')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Json.php';
			if (!class_exists('Zend\Json\Expr')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Expr.php';
			$this->view->js->append_inline('CKEDITOR.replace("'.$this->name.'", '.Zend\Json\Json::encode($opt, false, array(
				'enableJsonExprFinder' => true
			)).');');
			if ($this->ckeditor->class) $res = '<div class="'.$this->ckeditor->class.'">'.$res.'</div>';
		}
		return $res;
	}
}