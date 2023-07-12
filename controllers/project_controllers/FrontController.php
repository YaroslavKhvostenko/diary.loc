<?php

class FrontController
{
    /**
     * Necessary data: controller, action and if isset param values
     * @var array
     */
    private $uriData;

    /**
     * get a suitable uri
     * @return null|string
     */
    private function getUri()
    {
        return !empty($_SERVER['REQUEST_URI']) ? trim($_SERVER['REQUEST_URI'], '/') : null;
    }

    /**
     * Form an Necessary data on the basis of uri
     * FrontController constructor.
     */
    public function __construct()
    {
        $uri = $this->getUri();
        $uriSegments = explode('/', $uri);

        if (empty($uriSegments[0])) {
            $controller = 'IndexController';
            $action     = 'indexAction';
        } elseif (count($uriSegments) == 1) {
            $controller = ucfirst(strtolower($uriSegments[0])) . 'Controller';
            $action     = 'indexAction';
        } else {
            $controller = ucfirst(strtolower($uriSegments[0])) . 'Controller';
            $action     = strtolower($uriSegments[1]) . 'Action';
        }

        $param = isset($uriSegments[2]) ? (string)$uriSegments[2] : null;

        $this->uriData = [
            'controller'   => $controller,
            'action'       => $action,
            'param'        => $param
        ];
    }

    /**
     * Get body
     */
    public function route()
    {
        ob_start();
        header("Content-type: text/html; charset=utf-8");
        $this->run($this->uriData);
        echo ob_get_clean();
    }


    /**
     * Choose the correct controller and action using reflectionClass
     * if controller or action not correct insert error msg into 'error_reflection.log'
     * and runs with default error action
     * @param array $uriData
     */
    private function run(array $uriData)
    {
        try {
            $rc = new ReflectionClass($uriData['controller']);
            $controller = $rc->newInstance();
            $action = $rc->getMethod($uriData['action']);
            $action->invoke($controller, $uriData['param']);
        } catch (ReflectionException $reflectionException) {
            file_put_contents("errors/error_reflection.log",
                "Ошибка создания ReflectionClass или ReflectionMethod.\n" .
                "Ошибка: "      . $reflectionException->getMessage() .
                "\nФайл: "      . $reflectionException->getFile() .
                "\nСтрока: "    . $reflectionException->getLine() .
                "\n\n", FILE_APPEND | FILE_USE_INCLUDE_PATH);
            $this->run($this->getNotFoundAction());
        }
    }

    /**
     * if in URI string we received unknown Controller or action ,
     * this method returns array with 'IndexController' and 'notFoundAction'
     * @return array
     */
    private function getNotFoundAction()
    {
        return [
            'controller'    => 'IndexController',
            'action'        => 'notFoundAction',
            'param'         => null
        ];
    }

}