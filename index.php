<?php

interface DIInterface
{

}

interface DIBuilderInterface
{
    public static function make(string $class);

    public static function get(string $class);
}

interface RouteInterface
{
    public static function get(string $controller, string $action);
}

interface ModelInterface
{

}



trait DIBuilder
{
    public static $DI = [];

    public static function make(string $class)
    {
        if (isset(self::$DI[$class])) return self::$DI[$class];

        $instance = new $class;

        return self::$DI[$class] = $instance;
    }

    public static function get(string $class)
    {
        if (empty(self::$DI[$class])) {
            Throw new Exception(sprintf('DI Class %s does not exist.', self::$DI[$class]));
        }

        return self::$DI[$class];
    }
}

class DI implements DIInterface, DIBuilderInterface
{
    use DIBuilder;
}

class Route implements RouteInterface
{
    public static function get(string $controller, string $action)
    {
        $controller = new $controller;
        $r = new \ReflectionMethod($controller, $action);
        $params = $r->getParameters();
        $reflectionParams = [];
        foreach ($params as $param) {
            if (class_exists($param->name)) {
                DI::make($param->name);
                $reflectionParams[] = DI::get($param->name);
                continue;
            }

            $reflectionParams[] = $param->name;
        }

        // TODO Version 1
        //call_user_func_array([$controller, $action], $reflectionParams);

        // TODO Version 2
        $controller->$action(...$reflectionParams);
    }
}

abstract class Model implements ModelInterface
{
    protected function getClassName() : string
    {
        return self::class;
    }
}




//////////////////////////////////////////////////////// Start  ////////////////////////////////////////////////////////

class ModelA extends Model implements ModelInterface
{
    public function str(string $str): string
    {
        return $str;
    }
}

class ModelB extends Model implements ModelInterface
{
    public function arr(array $arr): array
    {
        return $arr;
    }
}

class NotModel
{
    public function test() : string
    {
        return "fsdf-----sdfdsf----";
    }
}

class tanz
{

}

class CookiesController
{
    public function index(ModelA $modelA, ModelB $modelB, tanz $tanz)
    {
        echo $modelA->str("1) test 777<br/>----<br> 2) ");

        var_dump($modelB->arr([1, 2]));
    }

    public function test(NotModel $notModel)
    {
        echo '<br> 3) ' . $notModel->test();
    }
}

Route::get(CookiesController::class, 'index');
Route::get(CookiesController::class, 'test');
