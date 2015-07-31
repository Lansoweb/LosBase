<?php

/**
 * Abstract Base Controller
 *
 * @package   LosBase\Controller
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Abstract Base Controller
 *
 * @package   LosBase\Controller
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
abstract class AbstractBaseController extends AbstractActionController
{
    protected $defaultSort = 'id';

    protected $defaultOrder = 'asc';

    protected $defaultPageSize = 20;

    protected $paginatorRange = 15;

    protected $uniqueField = null;

    protected $successAddMessage = 'Operação realizada com sucesso!';

    protected $successEditMessage = 'Operação realizada com sucesso!';

    protected $successDeleteMessage = 'Operação realizada com sucesso!';

    protected $errorDeleteMessage = 'Erro ao excluir entidade!';

    protected function getModuleName()
    {
        $module_array = explode('\\', get_class($this));

        return $module_array[0];
    }

    /**
     * Nome da rota raiz do controlador
     */
    public function getRouteName()
    {
        return strtolower($this->getModuleName());
    }

    protected function getAddForm()
    {
        return $this->getForm();
    }

    protected function getEditForm()
    {
        return $this->getForm();
    }

    /**
     * Retorna uma rota para a ação especificada ou a atual
     *
     * @param string $action
     */
    public function getActionRoute($action = null)
    {
        if (null === $action) {
            $action = $this->getEvent()
                ->getRouteMatch()
                ->getParam('action');
        }

        return $this->getRouteName().'/'.$action;
    }

    protected function needAddOther($form)
    {
        return false;
    }

    protected function validateDelete($post)
    {
        if (is_array($post) && array_key_exists('confirm', $post)) {
            $confirm = $post['confirm'];

            if ($confirm == "1") {
                return true;
            }
        }

        return false;
    }

    public function indexAction()
    {
        return [];
    }
}
