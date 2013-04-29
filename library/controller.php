<?php

class k_controller {
	public $layout;
	public $request;
	public $response;
	public $view;
	public $model = null;

	public function __construct($request = null, $response = null) {
		if ($request == null) $request = application::get_instance()->request;
		if ($response == null) $response = application::get_instance()->response;

		// Устанавливаем скрипт лейаута по-умолчанию
		$this->layout = application::get_instance()->config->layout;

		// Запоминаем request, response
		$this->request = $request;
		$this->response = $response;

		// Создаем экземпляр view, чтобы иметь возможность в него добавлять в контроллере
		$this->view = new view($this->request->controller.'/'.$this->request->action);

		// Находим и инициализируем модель по названию контроллера
		$parts = explode('_', get_class($this));
		$class = 'model_'.$parts[count($parts) - 1];
		if (class_exists($class)) $this->model = new $class();
	}

	public function __call($method, $args) {
		$method = null;
		$args = null;
		// Пусто. При несуществующем экшене прсто проваливаемся сюда вместо ошибки
	}

	public function before() {
		// Пусто. Ожидается реализация в наследнике
	}

	public function after() {
		// Пусто. Ожидается реализация в наследнике
	}

	public function render() {
		// Рендерим вьюшку
		$output = $this->view->render();
		if ($this->layout) {
			// Устанавливаем содержимое отренедеренной вьюшки в переменную content лейаута
			if ($output) $this->view->content = $output;

			// Рендерим лейаут
			$output = $this->view->render($this->layout);
		}

		// Добавляем в response отренедеренный текст
		$this->response->append($output);

		// Отсылаем заголовки
		$this->response->header_send();

		// Отсылаем тело страницы
		$this->response->send();
	}

	public function forward($action, $controller = null, $param = null) {
		$app = $this->layout = application::get_instance();
		$app->request->controller = $controller;
		if (!$app->request->controller) $app->request->controller = str_replace('controller_', '', get_class($app->controller));
		$app->request->action = $action;
		if ($param) $app->request->param = new data($param);
		$app->run_controller();
		exit();
	}
}