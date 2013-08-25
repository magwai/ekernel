<?php

class k_view_helper_control extends view_helper  {
	public $inited = false;
	public $config = null;

	public function control($data = null) {
		// Инициализируем админку настройками из конфига
		if (!$this->inited) {
			$config = application::get_instance()->config->control;
			$this->config = new data($config);
			$this->inited = true;
		}

		// Добавляем в настройки админки все что пришло в $data
		if ($data !== null) $this->config->set($data);
		return $this;
	}

	public function run() {
		// Читаем настройки по-умолчению
		$this->config_default();

		// Читаем настройки, зависящие от БД
		$this->config_db();

		// Запускаем вьюшку с настройками
		$this->config_view();

		// Производим постобработку настроек
		$this->config_finish();

		$active = $this->view->navigation()->find_active();
		if ($active) session::set('IsAuthorized', true);
		// Если у пользователя есть права для доступа в раздел - рендерим основную функцию генерации раздела
		// Иначе - на авторизацию
		if (
			($this->config->controller == 'cindex' && $this->config->action == 'index' && $this->view->user('id'))
				||
			($this->config->controller == 'cnotify' && ($this->config->action == 'read' || $this->config->action == 'mark'))
				||
			($this->config->controller == 'cuser' && ($this->config->action == 'login' || $this->config->action == 'logout'))
				||
			$active
		) {
			$method = 'route_'.$this->config->type;
			if (method_exists($this, $method)) $this->$method();
		}
		else {
			session::remove('IsAuthorized');
			$this->config->request->current = array(
				'controller' => 'cuser',
				'action' => 'login'
			);
		}

		// "Рендер" раздела. На деле - это заполнение контентных переменных в настройках админки из тех же самых настроек
		$this->render();
	}

	public function config_default() {
		// Вытаскиваем модель из контроллера
		$d = array();
		$class = 'model_'.$this->config->controller;
		if (class_exists($class)) $d['model'] = new $class;

		// Определяем тип из известных экшенов. Для остальных нужно будет указать во вьюшке
		if ($this->config->action == 'add') $d['type'] = 'add';
		if ($this->config->action == 'edit') $d['type'] = 'edit';
		if ($this->config->action == 'delete') $d['type'] = 'delete';
		if ($this->config->action == 'drag') $d['type'] = 'drag';

		// Сохраняем
		$this->config->set($d);
	}

	public function config_db() {
		if (!$this->config->model) return;

		// Читаем метаданные модели. Первично заполняем поля
		$meta = $this->config->model->metadata();
		if ($meta) {
			$config = application::get_instance()->config->control_field;
			foreach ($meta as $el) {
				$d = clone $config;

				// Тайтл по-умолчанию равен названию поля
				$d->title = $el['Field'];

				// Скрываем сервисные поля
				if ($el['Field'] == 'id' || $el['Field'] == 'parentid' || $el['Field'] == 'orderid') $d->active = false;

				// Некоторые настройки по-умолчанию для поля title
				if ($el['Field'] == 'title') {
					$d->title = $this->view->translate('control_field_title_title');
					$d->order = 1;
					$d->required = true;
				}

				// Сохраняем
				$this->config->field->{$el['Field']} = $d;
			}
		}
	}
	
	public function config_include($name, $param = array()) {
		$ret = null;
		$old_param = array();
		if ($param) foreach ($param as $k => $v) {
			if (isset($this->$k)) {
				$old_param[$k] = $this->$k;
				unset($this->$k);
			}
			$this->$k = $v;
		}
		$name_valid = substr($name, 0, 2) == 'k_' ? substr($name, 2) : $name;
		$fn = 'control/'.$name_valid.'.php';
		if ($name_valid == $name && file_exists(PATH_ROOT.'/'.DIR_APPLICATION.'/'.$fn)) $ret = include(PATH_ROOT.'/'.DIR_APPLICATION.'/'.$fn);
		else if (file_exists(PATH_ROOT.'/'.DIR_LIBRARY.'/'.$fn)) $ret = include(PATH_ROOT.'/'.DIR_LIBRARY.'/'.$fn);
		if ($param) foreach ($param as $k => $v) {
			unset($this->$k);
			if (isset($old_param[$k])) $this->$k = $old_param[$k];
		}
		return $ret;
	}

	public function config_view() {
		// Пробуем настройки из главной вьюшки
		$this->config_include($this->config->controller);

		// Пробуем настройки из вьюшки экшена
		$this->config_include($this->config->controller.'/'.$this->config->action);
	}

	public function config_finish() {
		// Проверяем тип раздела: если не заполнен, то ставим list при наличии модели и text при ее отсутствии
		if (!$this->config->type) $this->config->type = $this->config->model ? 'list' : 'text';

		// Подтягиваем конфиг из конфига типа
		if ($this->config->config_type->{$this->config->type} && count($this->config->config_type->{$this->config->type})) {
			$config_type = clone $this->config->config_type->{$this->config->type};
			$config_all = clone $this->config;
			unset($config_all->config_type);
			$this->config = new data($config_type);
			$this->config->set($config_all);
		}

		// Подтягиваем конфиг из конфига экшена
		if ($this->config->config_action->{$this->config->action} && count($this->config->config_action->{$this->config->action})) {
			$config = clone $this->config->config_action->{$this->config->action};
			unset($config->config_action);
			$this->config->set($config);
		}

		// Если заполнены поля
		if ($this->config->field) {
			// Если есть поле orderid, то включаем сортировку
			if (!isset($this->config->drag) && array_key_exists($this->config->field_map->orderid, $this->config->field->to_array())) $this->config->drag = true;

			if ($this->config->drag && !$this->config->param_default->orderby) $this->config->param_default->orderby = $this->config->field_map->orderid;

			// Сортируем поля по order
			$fields_order = array();
			$fields = $this->config->field->to_array();
			$config = application::get_instance()->config->control_field;
			foreach ($fields as $k => $v) {
				$fields_order[$k] = $v->order;

				// Для textarea ставим по-умолчанию 10 рядов
				if ($v->type == 'textarea' && !isset($v->rows)) $v->rows = 10;

				// Для селекта включаем плагин chosen
				if ($v->type == 'select' && !isset($v->chosen)) $v->chosen = true;

				// Для чекбокса включаем плагин uniform
				if ($v->type == 'checkbox' && !isset($v->uniform)) $v->uniform = true;

				// Для даты подключаем jquery ui
				if ($v->type == 'date') {
					if (!isset($v->ui) || !($v->ui instanceof data)) $v->ui = array();
					if (!isset($v->ui->opt)) $v->ui->opt = array();
					if (!isset($v->ui->opt->dateFormat)) $v->ui->opt->dateFormat = 'dd.mm.yy';
					if (!isset($v->ui->opt->constrainInput)) $v->ui->opt->constrainInput = true;
					if (!isset($v->ui->opt->changeYear)) $v->ui->opt->changeYear = true;
					if (!isset($v->ui->opt->changeMonth)) $v->ui->opt->changeMonth = true;
					if (!isset($v->ui->theme)) $v->ui->theme = $this->config->ui->theme;
					if (!isset($v->ui->lang)) $v->ui->lang = $this->config->ui->lang;
				}

				// Для файла включаем плагин uploadifive и устанавливаем пути по-умолчанию
				if ($v->type == 'file') {
					if (!isset($v->uploadifive) || !($v->uploadifive instanceof data)) $v->uploadifive = array();
					if (!isset($v->uploadifive->opt)) $v->uploadifive->opt = array();
					if (!isset($v->uploadifive->opt->buttonClass)) $v->uploadifive->opt->buttonClass = 'btn btn-primary';
					if (!isset($v->path)) $v->path = PATH_ROOT.'/'.DIR_UPLOAD.'/'.$this->config->controller.'_'.$k;
					if (!isset($v->url)) $v->url = '/'.DIR_UPLOAD.'/'.$this->config->controller.'_'.$k;;
					if (!isset($v->id)) $v->id = $k;
				}

				if ($v->type == 'textarea' && $v->ckeditor) {
					if (!($v->ckeditor instanceof data)) $v->ckeditor = array(
						'class' => 'c-ckeditor'
					);
				}

				// Дополнительно еще раз сливаем настройки поля по-умолчанию с данными поля
				// Это нужно, потому что мы во вьюшке могли добавить поля, не имеющие полных настроек
				$fields[$k] = array_merge($config->to_array(), $v->to_array());
			}
			array_multisort($fields_order, SORT_ASC, SORT_NUMERIC, $fields);
			unset($this->config->field);
			$this->config->field = $fields;

			// Находим первое видимое поле
			$first = null;
			foreach ($this->config->field as $k => $el) {
				if ($el->active && !$el->hidden) {
					// Запоминаем его
					if (!$first) $first = $k;

					// Если ранее не указали поле сортировки по-умолчанию, выставляем его равным первому полю
					if (!$this->config->param_default->orderby) $this->config->param_default->orderby = $k;
					break;
				}
			}

			// Проверяем, если ранее указали заглавное поле и такого поля нет - обнуляем его
			if ($this->config->cell_title && !$this->config->field->{$this->config->cell_title}) $this->config->cell_title = null;

			// Проверяем, если ранее не указали заглавное поле, то выставлем его либо в title (при его наличии), либо в первое видимое поле
			if (!$this->config->cell_title) $this->config->cell_title = $this->config->field->title ? 'title' : $first;

			// Если для заглавного поля не выставлен скрипт - выставляем скрипт по-умолчанию
			if ($this->config->field->{$this->config->cell_title} && !$this->config->field->{$this->config->cell_title}->script) $this->config->field->{$this->config->cell_title}->script = 'control/cell/title';
		}

		// Если не задано поле сортировки, то ставим поле по-умолчанию из реквеста. Это нужно, чтобы мы могли во вьюшке выставить свое поле сортировки по-умолчанию
		// То же самое с направлением сортировки
		if (!$this->config->param->orderby) $this->config->param->orderby = $this->config->param_default->orderby;
		if (!$this->config->param->orderdir) $this->config->param->orderdir = $this->config->param_default->orderdir;

		// Если мы рендерим дерево
		if ($this->config->tree) {
			// Добавляем в where условие для связи id - parentid
			$this->config->where->parentid = $this->config->param->oid;

			// Добавляем в расширенные поля значение parentid равное oid из параметров
			if ($this->config->type == 'add') $this->config->post_field_extend->parentid = $this->config->param->id ? $this->config->param->id : $this->config->param->oid;
		}

		if ($this->config->type == 'add' && $this->config->drag) {
			$this->config->post_field_extend->{$this->config->field_map->orderid} = $this->config->model->fetch_max($this->config->field_map->orderid) + 1;
		}

		if ($this->config->param->cid) {
			$this->config->where->parentid = $this->config->param->cid;
			$this->config->post_field_extend->parentid = $this->config->param->cid;
		}

		// Выставляем возвратный контроллер для всех типов завершения равным текущему
		if (!$this->config->request->success->controller) $this->config->request->success->controller = $this->config->controller;
		if (!$this->config->request->fail->controller) $this->config->request->fail->controller = $this->config->controller;
		if (!$this->config->request->cancel->controller) $this->config->request->cancel->controller = $this->config->controller;

		// Если у нас присутствует кнопка Apply, то возвратный экшн для типов завершения success и fail равно текущему
		if ($this->config->post->is_apply) {
			$this->config->request->success->action = $this->config->action;
			$this->config->request->fail->action = $this->config->action;
		}

		// При работе раздела типа edit добавляем в where условие выборки по id текущего объекта
		if ($this->config->type == 'edit') $this->config->where->id = $this->config->param->id;

		// Добавляем where из фильтра
		if ($this->config->type == 'list' && $this->config->field) {
			foreach ($this->config->field as $k => $v) {
				if ($v->search) {
					if (!($v->search instanceof data)) $v->search = array();
					$v->search->name = $k;
					$v->search->type = $v->search->type ? $v->search->type : 'text';
					if ($v->search->type == 'range') {
						$v->search->range_type = $v->search->range_type ? $v->search->range_type : 'number';
						if ($v->search->range_type == 'date') {
							if (!class_exists('Zend\Json\Encoder')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Encoder.php';
							if (!class_exists('Zend\Json\Json')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Json.php';
							if (!class_exists('Zend\Json\Expr')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Expr.php';
							$v->search->range_ui_param = $v->search->range_ui_param
								? $v->search->range_ui_param
								: array(
									'dateFormat' => 'dd.mm.yy',
									'constrainInput' => true,
									'changeYear' => true,
									'changeMonth' => true,
									'onSelect' => new Zend\Json\Expr('function() { c.filter_change(this, { keyCode: 13 }); }')
								);
						}
						if (!$v->search->default) $v->search->default = array('', '');
					}
					$v->search->match_mode = $v->search->match_mode ? $v->search->match_mode : ($v->search->type == 'select' ? 'exact' : 'like');
					if (!$v->search->script) $v->search->script = 'control/search/'.$v->search->type;
					if (isset($v->search->default)) {
						$val = $v->search->default->to_array();
						if ($v->search->type == 'range') $val = strlen(@$val[0]) > 0 && strlen(@$val[1]) > 0 ? implode(',', $val) : '';
						if (!isset($this->config->param->{'search_'.$k})) $this->config->param->{'search_'.$k} = $val;
						if (strlen($val) > 0) $this->config->param_default->{'search_'.$k} = $val;
					}
				}
				if (strlen($this->config->param->{'search_'.$k}) > 0) {
					if ($v->search->type == 'range') {
						$vals = explode(',', $this->config->param->{'search_'.$k});
						if ($v->search->range_type == 'date') $vals[0] = date('Y-m-d', strtotime($vals[0].' 00:00:00'));
						if (strlen($vals[0]) > 0) $this->config->where->{$k.' >= ?'} = $vals[0];
						if ($v->search->range_type == 'date') $vals[1] = date('Y-m-d', strtotime($vals[1].' 00:00:00'));
						if (strlen($vals[1]) > 0) $this->config->where->{$k.' <= ?'} = $vals[1];
					}
					else {
						if ($v->search->match_mode == 'exact') $this->config->where->$k = $this->config->param->{'search_'.$k};
						else $this->config->where->{$k.' LIKE ?'} = $this->config->model->adapter->quote('%'.$this->config->param->{'search_'.$k}.'%');
					}
				}
			}
		}

		if ($this->config->static_field) {
			if (!($this->config->static_field instanceof data)) $this->config->static_field = new data;
			if (!isset($this->config->static_field->field_dst)) $this->config->static_field->field_dst = $this->config->field_map->stitle;
			if (!isset($this->config->static_field->field_src)) $this->config->static_field->field_src = $this->config->field_map->title;
			if (!isset($this->config->static_field->length)) $this->config->static_field->length = 50;
			if (!isset($this->config->static_field->unique)) $this->config->static_field->unique = true;
		}

		// Получаем кнопки из меню
		if ($this->config->action == 'index') {
			$active = $this->view->navigation()->find_active();
			if ($active && count($active->pages)) {
				$button_top = $this->config->button_top;
				foreach ($active->pages as $el) {
					if ($el->is_inner) {
						$button_top[] = array(
							'title' => $el->title,
							'controller' => $el->controller,
							'action' => $el->action,
							'key' => 'cid',
							'pid' => $this->config->param->cid
						);
					}
				}
				$this->config->button_top = $button_top;
			}
		}

		// Строим верхние кнопки
		if (count($this->config->button_top)) {
			$button = clone $this->config->button_top;
			unset($this->config->button_top);
			$this->config->button_top = $this->button_build($button);
		}

		// Строим нижние кнопки
		if (count($this->config->button_bottom)) {
			$button = clone $this->config->button_bottom;
			unset($this->config->button_bottom);
			$this->config->button_bottom = $this->button_build($button);
		}

		// Заполняем место из типа раздела, если оно не заполнено
		if (!$this->config->place) $this->config->place = $this->view->translate('control_place_'.$this->config->type);

		//Устанавливаем для текущего роута значения по-умолчанию
		$this->view->url()->default = $this->config->param_default;

		// Формируем кнопки oac
		if (($this->config->type == 'add' || $this->config->type == 'edit') && count($this->config->oac)) {
			foreach ($this->config->oac as $k => $v) {
				if ($v == false) {
					unset($this->config->oac->$k);
					continue;
				}
				if (!($v instanceof data)) $this->config->oac->$k = new data();
				if (!isset($this->config->oac->$k->type)) $this->config->oac->$k->type = $k == 'ok' ? 'submit' : 'button';
				if (!isset($this->config->oac->$k->value)) $this->config->oac->$k->value = $this->view->translate('form_element_oac_'.$k.'_value');
				if (!isset($this->config->oac->$k->class)) $this->config->oac->$k->class = 'btn'.($k == 'ok' ? ' btn-success' : '').($k == 'cancel' ? ' btn-warning' : '').($k == 'apply' ? ' btn-default' : '');
				if (!isset($this->config->oac->$k->frame_view_script)) $this->config->oac->$k->frame_view_script = false;
			}

			if ($this->config->oac->ok) {
				if (!isset($this->config->oac->ok->order)) $this->config->oac->ok->order = 1;
			}

			if ($this->config->oac->cancel) {
				if (!isset($this->config->oac->cancel->order)) $this->config->oac->cancel->order = 2;
				if (!isset($this->config->oac->cancel->onclick)) {
					$p = clone $this->config->param;
					$p->set($this->config->request->current->param);
					$p['ccontroller'] = $this->config->request->cancel->controller;
					$p['caction'] = $this->config->request->cancel->action;
					unset($p['id']);
					unset($p['ids']);
					$this->config->oac->cancel->onclick = 'window.location = \''.$this->view->url($p, 'control').'\';return false;';
				}
			}

			if ($this->config->oac->apply) {
				if (!isset($this->config->oac->apply->order)) $this->config->oac->apply->order = 3;
				if (!isset($this->config->oac->apply->onclick))$this->config->oac->apply->onclick = '$(this).parents(\'form\').append(\'<input type=hidden name=is_apply value=1 />\').submit().find(\'input[name=is_apply]\').remove();return false;';
			}

			// Сортируем кнопки
			if (count($this->config->oac)) {
				$fields_order = array();
				$fields = $this->config->oac->to_array();
				foreach ($fields as $k => $v) $fields_order[$k] = $v->order;
				array_multisort($fields_order, SORT_ASC, SORT_NUMERIC, $fields);
				unset($this->config->oac);
				$this->config->oac = $fields;
			}
		}

		// Вызываем коллбэк для финальных настроек из вьюшки
		if ($this->config->callback->postconfig) {
			$f = $this->config->callback->postconfig;
			$f($this);
		}
	}

	public function button_build($button) {
		$data = array();
		if (!$button) return $data;

		$edit_index = null;
		$was_default = false;
		$n = 0;

		foreach ($button as $el) {
			if (!$el) continue;
			else {
				// Разворачиваем текстовые кнопки в нормальный вид массива
				$d = new data(
					is_string($el)
						? array(
							'title' => $this->view->translate('control_button_'.$el),
							'controller' => $this->config->controller,
							'action' => $el,
                                                        'default' => false
						)
						: $el
				);

				// Проверяем, была ли кнопка по-умолчанию
				if ($d->default) $was_default = true;

				// Кнопку delete делаем с подтверждением отправки
				if (!isset($el->confirm) && $d->action == 'delete') $d->confirm = true;

				$data[] = $d;

				// Ставим класс кнопки по-умолчанию
				if (!isset($el->class)) {
					if ($d->action == 'add') $d->class = 'btn-success';
					else if ($d->action == 'edit') $d->class = 'btn-warning';
					else if ($d->action == 'delete') $d->class = 'btn-danger';
					else $d->class = 'btn-primary';
				}

				// Запоминаем индекс кнопки edit
				if ($d->action == 'edit') $edit_index = $n;
				$n++;
			}
		}

		// Если кнопки по-умолчанию не было и была кнопка edit, то делаем ее по-умолчанию
		if (!$was_default && $edit_index !== null) $data[$edit_index]->default = true;

		return $data;
	}

	public function render() {
		// Записываем уведомления
		if (count($this->config->notify)) {
			$mn = new model_cnotify;
			foreach ($this->config->notify as $el) $mn->insert($el->to_array());
		}

		// Если заполнен текущий возвратный контроллер, то делаем редирект
		if (count($this->config->request->current)) {
			$p = clone $this->config->param;
			unset($p['id']);
			unset($p['ids']);
			//unset($p['oid']);
			unset($p['prev']);
			$p->set($this->config->request->current->param);
			$p['ccontroller'] = $this->config->request->current->controller;
			$p['caction'] = $this->config->request->current->action;
			header('Location: '.$this->view->url($p, 'control'));
			application::get_instance()->controller->layout = null;
		}

		// Вызываем коллбэк для финальных настроек из вьюшки перед рендером лейаута
		if ($this->config->callback->prelayout) {
			$f = $this->config->callback->prelayout;
			$f($this);
		}
	}

	public function route_form() {
		$this->config->form = new form(array(
			'class' => 'row-fluid c-form',
			'class_element_frame' => 'form-group col-8',
			'class_element_text' => 'form-control c-input',
			'class_element_select' => 'form-control c-select',
			'class_element_textarea' => 'form-control c-textarea',
			'error_view_script' => 'control/error'
		));
		if ($this->config->field) {
			$n = 0;
			foreach ($this->config->field as $k => $v) {
				if (!$v->active || $v->hidden) continue;
				$p = clone $v;
				$p->label = $p->title;
				$this->config->form->add($p->type, $k, $p);
				$n++;
			}
		}

		if ($this->config->meta) {
			$meta = $this->config_include('meta', array(
				'return_only' => true
			));
			if ($meta) {
				$meta = new data($meta);
				$field = $meta->field;
				unset($field->url);
				unset($field->controller);
				unset($field->data);
				$meta_array = array();
				foreach ($field as $k => $v) {
					if (!isset($v->type)) $v->type = 'text';
					$v->label = $v->title;
					$this->config->form->add($v->type, 'meta_'.$k, $v->to_array());
					$meta_array[] = 'meta_'.$k;
				}
				$this->config->form->add_display_group($meta_array, 'meta', array(
					'legend' => 'Дополнительно',
					'class' => 'c-meta c-invisible'
				));
			}
		}

		if (count($this->config->oac)) {
			$oac_array = array();
			foreach ($this->config->oac as $k => $v) {
				$this->config->form->add($v->type, 'oac_'.$k, $v->to_array());
				$oac_array[] = 'oac_'.$k;
			}
			$this->config->form->add_display_group($oac_array, 'oac', array(
				'class' => 'c-oac'
			));
		}

		if ($this->config->type == 'edit' && $this->config->model && $this->config->use_db) {
			$this->config->data = $this->config->model->fetch_control_card($this->config->where->to_array());
		}
		if ($this->config->type == 'edit' && (($this->config->model && $this->config->use_db && !count($this->config->data)) || ((!$this->config->model || !$this->config->use_db) && !$this->config->param->id))) {
			$this->config->notify[] = array(
				'title' => $this->view->translate('control_notify_'.$this->config->type.'_noel'),
				'style' => 'warning'
			);
			$this->config->request->current = $this->config->request->cancel;
			return;
		}

		$this->config->id = $this->config->data->id;
		if ($this->config->type == 'add' && !$this->config->id && $this->config->model && $this->config->use_db) {
			$this->config->id = method_exists($this->config->model, 'fetch_next_id') ? $this->config->model->fetch_next_id() : null;
		}

		if (count($this->config->post)) {
			if ($this->config->form->validate($this->config->post)) {
				$this->config->data_old = clone $this->config->data;
				unset($this->config->data);
				$this->config->data = $this->config->form->get();

				if (count($this->config->post_field_extend)) $this->config->data = $this->config->post_field_extend;

				if ($this->config->static_field && !@$this->config->data->{$this->config->static_field->field_dst} && $this->config->type == 'add') {
					$stitle = common::stitle($this->config->data[$this->config->static_field->field_src], $this->config->static_field->length);
					$stitle = $stitle ? $stitle : '_';
					$stitle_n = $stitle;
					if ($this->config->static_field->unique && $this->config->use_db) {
						$stitle_p = -1;
						do {
							$stitle_p++;
							$stitle_n = $stitle.($stitle_p == 0 ? '' : $stitle_p);
							$w = array('`'.$this->config->static_field->field_dst.'` = ?' => $stitle_n);
							$stitle_c = (int)$this->config->model->fetch_count($w);
						}
						while ($stitle_c > 0);
					}
					$this->config->data[$this->config->static_field->field_dst] = $stitle_n;
				}

				$this->config->ok = true;

				if ($this->config->callback->before) {
					$f = $this->config->callback->before;
					$f($this);
				}

				if ($this->config->ok) {
					$this->config->m2m_changed = false;
					$this->config->meta_changed = false;
					foreach ($this->config->data as $k => $v) {
						if (@$this->config->field->$k->m2m) {
							$m2m_new = $this->config->data->$k ? $this->config->data->$k->to_array() : array();
							$m2m_orderid = (int)$this->config->field->$k->m2m->orderid;
							$m2m_model_class = $this->config->field->$k->m2m->model;
							$m2m_model = new $m2m_model_class();
							$m2m_self = $this->config->field->$k->m2m->self;
							$m2m_foreign = $this->config->field->$k->m2m->foreign;

							$m2m_old = $m2m_model->fetch_all(array(
								$m2m_self => $this->config->id
							));

							$m2m_ids = array();
							if ($m2m_old) {
								// Удаляем несуществующие связи
								foreach ($m2m_old as $m2m_el) {
									if (!$m2m_new || !in_array($m2m_el->$m2m_foreign, $m2m_new)) {
										$this->config->m2m_changed = true;
										$m2m_model->delete(array(
											'id' => $m2m_el->id
										));
									}
									else $m2m_ids[] = $m2m_el->$m2m_foreign;
								}
							}
							// Добавляем
							if ($m2m_new) {
								foreach ($m2m_new as $m2m_el) {
									if (!in_array($m2m_el, $m2m_ids)) {
										$this->config->m2m_changed = true;
										$m2md = array(
											$m2m_self => $this->config->id,
											$m2m_foreign => $m2m_el
										);
										if ($m2m_orderid) {
											$nid = $m2m_model->fetch_max('orderid');
											$m2md['orderid'] = $nid + 1;
										}
										$m2m_model->insert($m2md);
									}
								}
							}
							unset($this->config->data->$k);
						}
					}
					if ($this->config->model && $this->config->use_db) {
						if ($this->config->meta) {
							$model_meta = new model_meta;
							$meta_data = array();
							$this->config->form->group->meta->validate($this->config->post);
							$meta_post = $this->config->form->group->meta->get();
							if (count($meta_post)) foreach ($meta_post as $k => $v) {
								if (substr($k, 0, 5) != 'meta_') continue;
								$v = trim($v);
								if ($v) $meta_data[substr($k, 5)] = $v;
							}
							$use_meta = $ex = $model_meta->fetch_row(array(
								'controller' => $this->config->controller,
								'parentid' => $this->config->id
							));
							if (!$ex && $meta_data) $use_meta = true;
							if ($use_meta) {
								$meta_d = json_encode($meta_data);
								if ($ex) {
									if (!$meta_data) $this->config->meta_changed = $model_meta->delete(array(
										'id' => $ex->id
									));
									else $this->config->meta_changed = $model_meta->update(array(
										'data' => $meta_d
									), array(
										'id' => $ex->id
									));
								}
								else $this->config->meta_changed = $model_meta->insert(array(
									'data' => $meta_d,
									'controller' => $this->config->controller,
									'parentid' => $this->config->id
								));
							}
						}
						$data = $this->config->data->to_array();

						if ($data) {
							$meta = $this->config->model->metadata();
							foreach ($data as $k => $v) {
								if (!array_key_exists($k, $meta)) unset($data[$k]);
							}
						}
						if ($this->config->type == 'add') {
							$this->config->ok = $this->config->model->insert_control($data);
						}
						else {
							$this->config->ok = $this->config->model->update_control($data, $this->config->where->to_array());
						}
					}
				}

				if (!$this->config->ok) $this->config->ok = $this->config->m2m_changed || $this->config->meta_changed;
				if ($this->config->callback->after) {
					$f = $this->config->callback->after;
					$f($this);
				}
				if ($this->config->ok) {
					if (!$this->config->stop_info) $this->config->notify[] = array(
						'title' => $this->view->translate('control_notify_'.$this->config->type.'_success'),
						'style' => 'success'
					);
					$this->config->request->current = $this->config->request->success;
					if ($this->config->callback->success) {
						$f = $this->config->callback->success;
						$f($this);
					}
				}
				else {
					$this->config->request->current = $this->config->request->fail;
					if ($this->config->callback->fail) {
						$f = $this->config->callback->fail;
						$f($this);
					}
				}
				return;
			}
		}
		else {
			if (count($this->config->form->element) && $this->config->type == 'edit') {
				foreach ($this->config->form->element as $el) {
					$k = $el->name;
					if ($this->config->field->$k && $this->config->field->$k->m2m) {
						$m2m_model = $this->config->field->$k->m2m->model;
						$m2m_self = $this->config->field->$k->m2m->self;
						$m2m_foreign = $this->config->field->$k->m2m->foreign;
						$this->config->data->$k = $m2m_model->fetch_col($m2m_foreign, array(
							$m2m_self => $this->config->id
						));
					}
				}
			}
			if ($this->config->meta) {
				$model_meta = new model_meta;
				$meta_data_raw = $model_meta->fetch_by_controller($this->config->controller, $this->config->id);
				if ($meta_data_raw) {
					$meta_data = array();
					foreach ($meta_data_raw as $k => $v) $meta_data['meta_'.$k] = $v;
					$this->config->form->group->meta->populate($meta_data);
				}
			}
		}
		if ($this->config->callback->preset) {
			$f = $this->config->callback->preset;
			$f($this);
		}
		if (!count($this->config->post)) {
			$this->config->form->populate($this->config->data->to_array());
		}
	}

	public function route_add() {
		$this->route_form();
		$this->config->content = (string)$this->config->form;
	}

	public function route_edit() {
		$this->route_form();
		$this->config->content = (string)$this->config->form;
	}

	public function route_delete() {
		if ($this->config->callback->before) {
			$f = $this->config->callback->before;
			$f($this);
		}
		$ids = explode(',', $this->config->param->ids);
		if (!$ids[0]) $ids = array();
		if (!$ids && $this->config->param->id) $ids = array($this->config->param->id);

		$this->config->count = 0;
		$this->config->ok = $this->config->model && $this->config->use_db ? false : true;

		if (count($ids)) {
			foreach ($ids as $id) {
				$this->config->id = $id;

				if (!$this->config->model || !$this->config->use_db) $this->config->ok_el = true;

				if ($this->config->callback->before_el) {
					$f = $this->config->callback->before_el;
					$f($this);
				}

				if ($this->config->model && $this->config->use_db) {
					$this->config->where->id = $this->config->id;
					$this->config->ok_el = $this->config->model->delete_control($this->config->where->to_array());
				}

				if ($this->config->ok_el) {
					$this->config->ok = true;
					$this->config->count++;
				}

				if ($this->config->callback->after_el) {
					$f = $this->config->callback->after_el;
					$f($this);
				}
			}
		}
		else {
			$this->config->notify[] = array(
				'title' => $this->view->translate('control_notify_delete_noel'),
				'style' => 'warning'
			);
			$this->config->request->current = $this->config->request->cancel;
			return;
		}

		if ($this->config->callback->after) {
			$f = $this->config->callback->after;
			$f($this);
		}
		if ($this->config->ok) {
			$this->config->request->current = $this->config->request->success;
			$this->config->notify[] = array(
				'title' => sprintf($this->view->translate('control_notify_delete_success'), $this->config->count),
				'style' => 'success'
			);
			if ($this->config->callback->success) {
				$f = $this->config->callback->success;
				$f($this);
			}
		}
		else {
			$this->config->request->current = $this->config->request->fail;
			if ($this->config->callback->fail) {
				$f = $this->config->callback->fail;
				$f($this);
			}
		}
		return;
	}

	public function route_drag() {
		$cur = $this->config->model->fetch_control_card(array('id' => (int)$this->config->param->id));
    	$prev = $this->config->model->fetch_control_card(array('id' => (int)$this->config->param->prev));
    	$ok = false;
    	if ($cur) {
			$ok = $this->config->model->update_control(array(
				$this->config->field_map->orderid => @(int)$prev->{$this->config->field_map->orderid} + 1
			), array(
				'id' => $cur->id
			));
			if ($ok) {
	    		$w = array('`id` != ?' => $cur->id);
	    		if ($this->config->tree) $w[$this->config->field_map->parentid] = $cur->{$this->config->field_map->parentid};
		    	if ($prev) $w['`'.$this->config->field_map->orderid.'` > ?'] = $prev->{$this->config->field_map->orderid};
	    		$next = $this->config->model->fetch_col('id', $w);
	    		if ($next) $ok = $this->config->model->update_control(array($this->config->field_map->orderid => new database_expr('`'.$this->config->field_map->orderid.'` + 1')), '`id` IN ('.implode(',', $next).')');
	    	}
    	}
    	if ($ok) {
			$this->config->notify[] = array(
				'title' => $this->view->translate('control_notify_drag_moved'),
				'style' => 'success'
			);
    		if ($this->config->callback->success) {
				$f = $this->config->callback->success;
				$f($this);
			}
    	}
    	else $this->config->notify[] = array(
			'title' => $this->view->translate('control_notify_drag_not_moved'),
			'style' => 'warning'
		);
		$this->config->request->current = $this->config->request->success;
	}

	public function route_list() {
		$active = $this->view->navigation()->find_active();
		if ($active && $active->is_inner && !$this->config->param->cid) {
			$this->config->notify[] = array(
				'title' => $this->view->translate('control_notify_link_noel'),
				'style' => 'warning'
			);
			$this->config->request->current = array(
				'controller' => $active->parent->controller,
				'action' => $active->parent->action
			);
			if ($this->config->param->pid) {
				if (!$this->config->request->current->param) $this->config->request->current->param = new data;
				$this->config->request->current->param->cid = $this->config->param->pid;
			}
			unset($this->config->param->pid);
			return;
		}
		$is_model = $this->config->model && !count($this->config->data);
		$this->config->content = $this->view->xlist(array(
			'fetch' => array(
				'model' => $is_model ? $this->config->model : null,
				'method' => 'fetch_control_list',
				'param' => array(
					$this->config->where,
					$this->config->param->orderby.' '.$this->config->param->orderdir,
					$this->config->param->perpage,
					($this->config->param->page - 1) * $this->config->param->perpage
				),
				'data' => $is_model ? null : $this->config->data
			),
			'view' => array(
				'script' => 'control/table'
			),
			'pager' => array(
				'script' => 'control/pager',
				'style' => 'sliding',
				'perpage' => $this->config->param->perpage,
				'page' => $this->config->param->page
			),
			'callback' => array(
				'empty' => function($xlist) {
					if ($xlist->view->control()->config->tree && $xlist->view->control()->config->param->oid) {
						if (count($xlist->view->control()->config->button_top)) {
							foreach ($xlist->view->control()->config->button_top as $el) {
								if ($el->default && $xlist->view->control()->config->use_db) {
									$card = $xlist->view->control()->config->model->fetch_control_card(array(
										'id' => $xlist->view->control()->config->param->oid
									));
									if ($card) {
										$p = clone $xlist->view->control()->config->param;
										if ($el->param) $p = array_merge($p, $el->param);
										$p->id = $p->oid;
										$p->oid = $card->parentid;
										$xlist->view->control()->config->request->current = array(
											'controller' => $el->controller,
											'action' => $el->action,
											'param' => $p
										);
									}
									break;
								}
							}
						}
					}
				}
			)
		));
	}

	public function route_text() {
		if ($this->config->callback->before) {
			$f = $this->config->callback->before;
			$f($this);
		}
		$this->config->content = $this->config->text;
		if ($this->config->callback->after) {
			$f = $this->config->callback->after;
			$f($this);
		}
	}
}
