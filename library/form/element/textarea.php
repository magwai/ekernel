<?php

class k_form_element_textarea extends form_element {
	public $cols = null;
	public $rows = 10;
	public $view_script = 'form/textarea';

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (isset($param['markitup'])) {
			if (!$param['markitup'] instanceof data) $param['markitup'] = new data(is_array($param['markitup']) ? $param['markitup'] : array());
			if (!isset($param['markitup']->opt)) $param['markitup']->opt = new data();
			if (!isset($param['markitup']->set)) $param['markitup']->set = new data();
			if (!isset($param['markitup']->style)) $param['markitup']->style = 'html';
			if (!isset($param['markitup']->skin)) $param['markitup']->skin = 'markitup';
			if (!isset($param['markitup']->class)) $param['markitup']->class = '';
			$param['markitup']->set = array(
				'def' => array(),
				'html' => array(
					'onShiftEnter' => array('keepDefault' => false, 'replaceWith' => "<br />\n"),
					'onCtrlEnter' => array('keepDefault' => false, 'openWith' => "\n<p>", 'closeWith' => "</p>\n"),
					'onTab' => array('keepDefault' => false, 'openWith' => '	 '),
					'markupSet' => array(
						array('name' => 'Heading 1', 'key' => '1', 'openWith' => '<h1(!( class="[![Class]!]")!)>', 'closeWith' => '</h1>', 'placeHolder' => 'Your title here...'),
						array('name' => 'Heading 2', 'key' => '2', 'openWith' => '<h2(!( class="[![Class]!]")!)>', 'closeWith' => '</h2>', 'placeHolder' => 'Your title here...'),
						array('name' => 'Heading 3', 'key' => '3', 'openWith' => '<h3(!( class="[![Class]!]")!)>', 'closeWith' => '</h3>', 'placeHolder' => 'Your title here...'),
						array('name' => 'Heading 4', 'key' => '4', 'openWith' => '<h4(!( class="[![Class]!]")!)>', 'closeWith' => '</h4>', 'placeHolder' => 'Your title here...'),
						array('name' => 'Heading 5', 'key' => '5', 'openWith' => '<h5(!( class="[![Class]!]")!)>', 'closeWith' => '</h5>', 'placeHolder' => 'Your title here...'),
						array('name' => 'Heading 6', 'key' => '6', 'openWith' => '<h6(!( class="[![Class]!]")!)>', 'closeWith' => '</h6>', 'placeHolder' => 'Your title here...'),
						array('name' => 'Paragraph', 'openWith' => '<p(!( class="[![Class]!]")!)>', 'closeWith' => '</p>'),
						array('separator' => '---------------'),
						array('name' => 'Bold', 'key' => 'B', 'openWith' => '(!(<strong>|!|<b>)!)', 'closeWith' => '(!(</strong>|!|</b>)!)'),
						array('name' => 'Italic', 'key' => 'I', 'openWith' => '(!(<em>|!|<i>)!)', 'closeWith' => '(!(</em>|!|</i>)!)'),
						array('name' => 'Stroke through', 'key' => 'S', 'openWith' => '<del>', 'closeWith' => '</del>'),
						array('separator' => '---------------'),
						array('name' => 'Ul', 'openWith' => "<ul>\n", 'closeWith' => "</ul>\n"),
						array('name' => 'Ol', 'openWith' => "<ol>\n", 'closeWith' => "</ol>\n"),
						array('name' => 'Li', 'openWith' => '<li>', 'closeWith' => '</li>'),
						array('separator' => '---------------'),
						array('name' => 'Picture', 'key' => 'P', 'replaceWith' => '<img src="[![Source:!:http://]!]" alt="[![Alternative text]!]" />'),
						array('name' => 'Link', 'key' => 'L', 'openWith' => '<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!)>', 'closeWith' => '</a>', 'placeHolder' => 'Your text to link...'),
						array('separator' => '---------------'),
						array('name' => 'Clean', 'className' => 'clean', 'replaceWith' => new Zend\Json\Expr('function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") }')),
						array('name' => 'Preview', 'className' => 'preview', 'call' => 'preview')
					)
				)
			);
			$param['markitup']->opt = $param['markitup']->set->def;
			$param['markitup']->opt = $param['markitup']->set->{$param['markitup']->style};
			if (!$param['markitup']->set_style) $param['markitup']->set_style = '/library/ctl/markitup/sets/'.$this->markitup->style.'/style.css';
			$this->markitup = $param['markitup'];
		}
		if (isset($param['ckeditor'])) {
			if (!$param['ckeditor'] instanceof data) $param['ckeditor'] = new data(is_array($param['ckeditor']) ? $param['ckeditor'] : array());
			if (!isset($param['ckeditor']->opt)) $param['ckeditor']->opt = new data();
			if (!isset($param['ckeditor']->set)) $param['ckeditor']->set = new data();
			if (!isset($param['ckeditor']->style)) $param['ckeditor']->style = 'full';
			if (!isset($param['ckeditor']->class)) $param['ckeditor']->class = '';
			$param['ckeditor']->set = array(
				'def' => array(
					'width' => '100%',
					'allowedContent' => true,
					'forcePasteAsPlainText' => true
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
		if (!function_exists('to_array')) {
			function to_array(&$obj) {
				if ($obj instanceof data) $obj = $obj->to_array();
				if (is_array($obj)) foreach ($obj as &$el) to_array($el);
			}
		}
		if (@$this->markitup) {
			$opt = clone $this->markitup->opt;
			to_array($opt);
			$this->view->css->append('/library/ctl/markitup/skins/'.$this->markitup->skin.'/style.css');
			$this->view->css->append($this->markitup->set_style);
			$this->view->js->append('/library/ctl/markitup/jquery.markitup.js');
			$this->view->js->append_inline('$("textarea[name=\''.$this->name.'\']").markItUp('.Zend\Json\Json::encode($opt, false, array(
				'enableJsonExprFinder' => true
			)).');');
			if ($this->markitup->class) $res = '<div class="'.$this->markitup->class.'">'.$res.'</div>';
		}
		if (@$this->ckeditor) {
			$opt = clone $this->ckeditor->opt;
			to_array($opt);

			$this->view->js->append('/library/ctl/ckeditor/ckeditor.js');
			$this->view->js->append('/library/ctl/ckfinder/ckfinder.js');
			$this->view->js->append_inline('var editor_'.$this->name.' = CKEDITOR.replace("'.$this->name.'", '.Zend\Json\Json::encode($opt, false, array(
				'enableJsonExprFinder' => true
			)).');CKFinder.setupCKEditor(editor_'.$this->name.', "/library/ctl/ckfinder/");');
			if ($this->ckeditor->class) $res = '<div class="'.$this->ckeditor->class.'">'.$res.'</div>';
		}
		return $res;
	}
}