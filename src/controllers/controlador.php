<?php

namespace Controllers;

use \Slim\Container;

/**
 * Clase de controlador genérico que sirve como base para la aplicación
 */

class Controller
{

    // la aplicación
    protected $app;
    
    // el contenedor de inyección de dependencias de la aplicación
    protected $container;

    /**
     * Constructor de la clase Controller
     * @param type \Slim\Container $container - DIC
     */
    public function __construct(Container $container)
    {
        $this->app = Slim\Slim::getInstance();
        $this->container = $container;
    }

    
    /**
     * Toma la variable del método GET de una petición http
     * @param type string $key - el parametro que se busca
     */
    public function httpGet($key)
    {
        // busca el parametro de la consulta en la consulta completa
        if(isset($this->container->request->getQueryParams()[$key])) {
            return $this->container->request->getQueryParams()[$key];
        } else {
            return null; // si no hay parametro regresa un objeto nulo
        }
    }

    /**
     * Toma la variable del método POST de una petición http
     *  @param type string $key - el parametro que se busca
     */
    public function httpPost($key)
    {
        // busca el parametro en el cuerpo parseado de la petición
        if (isset($this->container->request->getParsedBody()[$key])) {
            return $this->container->request->getParsedBody()[$key];
        } else {
            return null;
        }
    }

    /**
     * Convierte un objecto en una cadena en formato JSON
     * @param type object $data - el objeto a convertir
     */
    public static function($data)
    {
        // si el parametro que se recibe no es objeto, regresa null
        if (!is_object($data)) {
            return null;
        } else {
            /* codifica el objeto a JSON de manera legible
               y con la constante de final de linea para manejar los saltos de linea en multiplataforma*/
            return json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
        }
    }

   /**
     * Crea una cadena de error en formato de JSON
     * @param type string $code - código del error
     * @param type string $path - ruta del error
     * @param type string $status - estado del error
     * @param type string $extra - información adicional
     * @return string - el error en formato JSON
     */
    public static function error($code, $path, $status, $extra = '')
    {
        $error = new \StdClass;
        
        $error->error = [
            'code' => $code,
            'path' => $path,
            'status' => $status
        ];
        
        if ($extra) {
            $error->error['extra'] = $extra;
        }
        return self::encode($error);
    }

    /**
     * Verifica una lista de validaciones
     * @param type array $valid - lista de validaciones
     */
    public static function valida($valid)
    {
        if (is_array($valid){
            foreach($valid as $v){
                if ($v == false){
                    return false; // regresa false si una validación es falsa
                } 
            } 
            return true;
        } else { return false; } // si no es un arreglo
    }
}