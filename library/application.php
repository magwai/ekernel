<?php

class k_application {
	public static $instance = null;
	public $plugin = array();
	public $bootstrap;
	public $router;
	public $request;
	public $response;
	public $controller;
	public $config = array();

	public function __construct() {
		// Читаем конфиги. Сначала из ядра, затем из приложения
		$this->config = new data(
			require PATH_ROOT.'/'.DIR_LIBRARY.'/config.php',
			require PATH_ROOT.'/'.DIR_APPLICATION.'/config.php'
		);
		// Инициализируем объекты request, response и router, так как они могут нам понадобиться в bootstrap
		$this->request = new request();
		$this->router = new router($this->config->route);
		$this->response = new response();
	}

	public function bootstrap() {
		// Загружаем все ресурсы приложения, указанные в конфиге
		$this->bootstrap = new bootstrap();
		$this->bootstrap->bootstrap($this->config->resource);
		return $this;
	}

	public function run() {
		// Запускаем роутинг и находим текущие контроллер и экшн
		$this->router->run($this->request);

		// Сливаем конфиг контроллера с основным конфигом
		if (isset($this->config->controller->{$this->request->controller})) {
			$this->config = new data(
				$this->config,
				$this->config->controller->{$this->request->controller}
			);
		}

		// Инициализируем плагины
		if ($this->config->plugin) {
			foreach ($this->config->plugin as $k => $v) {
				if (!is_object($v)) {
					$k = $v;
					$v = new data();
				}
				$class = 'plugin_'.$k;
				$this->plugin[$k] = new $class($v);
			}
		}

		$this->run_controller();
	}

	public function run_controller() {
		// Инициализируем класс контроллера
		$class = 'controller_'.$this->request->controller;
		if (!class_exists($class)) error::call('Not Found', 404);
		$this->controller = new $class($this->request, $this->response);

		$this->plugin_action('controller_before');

		// Хук, до выполнения экшена контроллера
		$this->controller->before();

		// Запускаем основной экшн
		$action = $this->request->action.'_action';
		$this->controller->$action($this->request->param);

		$this->plugin_action('controller_after');

		// Хук, после выполнения экшена контроллера
		$this->controller->after();

		// Запускаем ренедер контента
		$this->controller->render();

		$this->plugin_action('controller_render');
	}

	public static function get_instance() {
		if (self::$instance == null) {
			// Инициализируем автолоадер. Он ищет сначала в каталоге приложения, если класса нет и есть такой же с префиксом k_ в ядре, содает класс обертку с запрашиваемым именем и наследует ее от найденного в ядре класса
			spl_autoload_register(function($class) {
				$is_kernel = substr($class, 0, 2) == 'k_';
				$fn = str_replace('_', '/', $is_kernel ? preg_replace('/^k\_/', '', $class) : $class).'.php';
				if (!$is_kernel && file_exists(PATH_ROOT.'/'.DIR_APPLICATION.'/'.$fn)) require_once PATH_ROOT.'/'.DIR_APPLICATION.'/'.$fn;
				else if (file_exists(PATH_ROOT.'/'.DIR_LIBRARY.'/'.$fn)) {
					require_once PATH_ROOT.'/'.DIR_LIBRARY.'/'.$fn;
					if (!$is_kernel && class_exists('k_'.$class)) eval('class '.$class.' extends k_'.$class.' {};');
				}
			});
			// Экземпляр приложения создается не из текущего класса, а из класса, который находится в папке приложения
			application::$instance = new application();
		}
		return application::$instance;
	}

	public function plugin_action($name) {
		if ($this->plugin) {
			foreach ($this->plugin as $k => $v) {
				$v->$name($this->request);
			}
		}
	}
}