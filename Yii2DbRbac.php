<?php
/**
 * Yii2DbRbac for Yii2
 *
 * @author Elle <elleuz@gmail.com>
 * @version 1.1
 * @package Yii2DbRbac for Yii2
 *
 */
namespace developeruz\db_rbac;

use Yii;

class Yii2DbRbac extends \yii\base\Module
{
    public $controllerNamespace = 'developeruz\db_rbac\controllers';
    public $theme = false;
    public $userClass;
    public $accessRoles;

    public function init()
    {
        parent::init();
        $this->registerTranslations();

        if ($this->theme) {
            Yii::$app->view->theme = new \yii\base\Theme($this->theme);
        }
    }

    public function registerTranslations()
    {
        if (!isset(Yii::$app->i18n->translations['db_rbac'])) {
            Yii::$app->i18n->translations['db_rbac'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'ru-Ru',
                'basePath' => '@developeruz/db_rbac/messages',
            ];
        }
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/db_rbac/' . $category, $message, $params, $language);
    }

    /**
     * Check if current user has access to given route array.
     *
     * @param array $routes  Array of routes.
     * <code>
     * [
     *      'amin/index',
     *      'permit/access/user',
     *      'page',
     * ]
     * </code>
     *
     * @return boolean
     */
    public static function checkRouteAccess($routes){
        foreach ($routes as $routeVariant) {
            $rs = self::createPartRoutes($routeVariant);
            foreach ($rs as $route){
                if (Yii::$app->user->can($route)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Used to check if user has some special accesses to turn on or off some items in pages.
     * <code>
     * [
     *    'dashboard-finance,
     *    'dashboard-business',
     * ]
     * </code>
     *
     * @param array $accesses   List of accesses to check if user has access in some items in page or not.
     *
     * @return boolean
     */
    public static function checkInPageAccess($accesses){
        foreach ($accesses as $access){
            if(Yii::$app->getUser()->can("#".$access)){
                return true;
            }
        }

        return false;
    }

    /**
     * Split a rute and it accesseable items to check for permision.
     *
     * @param string    $route
     *
     * @return array
     */
    public static function createPartRoutes($route)
    {
        //$route[0] - is the route, $route[1] - is the associated parameters

        $routePathTmp = explode('/', trim($route, '/'));
        $result = [];
        $routeVariant = array_shift($routePathTmp);
        $result[] = $routeVariant;

        foreach ($routePathTmp as $routePart) {
            $routeVariant .= '/' . $routePart;
            $result[] = $routeVariant;
        }
        return $result;
    }
}
