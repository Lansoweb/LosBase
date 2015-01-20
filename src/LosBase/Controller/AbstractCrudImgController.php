<?php

/**
 * Abstract CRUD Controller
 *
 * @package   LosBase\Controller
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Controller;

use Zend\View\Model\ViewModel;
use Zend\Stdlib\ResponseInterface as Response;

/**
 * Abstract CRUD Controller
 *
 * @package   LosBase\Controller
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
abstract class AbstractCrudImgController extends AbstractCrudController
{

    /**
     * Image directory for entities, relative to project's root
     *
     * @var string
     */
    protected $imageDir = null;

    /**
     * Url for image dir, relative to project's url
     *
     * @var string
     */
    protected $imageUrl = null;

    abstract public function getImageDir();
    abstract public function getImageUrl();

    /**
     * Lista as entidades, suporte a paginação, ordenação e busca
     */
    public function listaAction()
    {
        $viewModel = parent::listaAction();
        $viewModel->setVariable('imageUrl', $this->getImageUrl());
        $viewModel->setVariable('imagemPath', $this->getImageUrl());

        return $viewModel;
    }

    /**
     * Insere ou Altera uma entidade
     */
    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

            if (method_exists($this, 'getEditForm')) {
            $form = $this->getEditForm();
        } else {
            $form = $this->getForm();

            $uploaded = new \Zend\Form\Element\Hidden('uploaded');
            // $uploaded->setValue('');
            $form->add($uploaded, [
                'priority' => - 100
            ]);

            $submitElement = new \Zend\Form\Element\Button('submit');
            $submitElement->setAttributes([
                'type' => 'submit',
                'class' => 'btn btn-primary'
            ]);
            $submitElement->setLabel('Salvar');
            $form->add($submitElement, [
                'priority' => - 100
            ]);

            $cancelarElement = new \Zend\Form\Element\Button('cancelar');
            $cancelarElement->setAttributes([
                'type' => 'button',
                'class' => 'btn',
                'onclick' => 'top.location="' . $this->url()
                    ->fromRoute($this->getActionRoute('lista')) . '"'
            ]);
            $cancelarElement->setLabel('Cancelar');
            $form->add($cancelarElement, [
                'priority' => - 100
            ]);
        }

        $redirectUrl = $this->url()->fromRoute($this->getActionRoute()) . ($redirect ? '?redirect=' . $redirect : '');
        $prg = $this->prg($redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            $id = $this->getEvent()
                ->getRouteMatch()
                ->getParam('id', 0);

            if ($id > 0) {
                $em = $this->getEntityManager();
                $objRepository = $em->getRepository($this->getEntityClass());
                $entity = $objRepository->find($id);
                if ($entity->getInputFilter() !== null) {
                    $form->setInputFilter($entity->getInputFilter());
                } else {
                    $entity->setInputFilter($form->getInputFilter());
                }
                $form->bind($entity);

                $idForm = new \Zend\Form\Element\Hidden('id');
                $idForm->setValue($id);
                $form->add($idForm, [
                    'priority' => - 100
                ]);
            } else {
                $classe = $this->getEntityClass();
                $entity = new $classe();
                $form->get('id')->setValue(0);
            }

            $this->getEventManager()->trigger('getForm', $this,
                [
                    'form' => $form,
                    'entityClass' => $this->getEntityClass(),
                    'id' => $id,
                    'entity' => $entity
                ]);

            return [
                'entityForm' => $form,
                'redirect' => $redirect,
                'entity' => $entity,
            ];
        }

        $post = $prg;

        $id = $post['id'];
        if ($id > 0) {
            $em = $this->getEntityManager();
            $objRepository = $em->getRepository($this->getEntityClass());

            $entity = $objRepository->find($id);
        } else {
            $classe = $this->getEntityClass();
            $entity = new $classe();
        }

        $this->getEventManager()->trigger('getForm', $this,
            [
                'form' => $form,
                'entityClass' => $this->getEntityClass(),
                'id' => $id,
                'entity' => $entity,
                'post' => $post
            ]);

        $savedEntity = $this->getEntityService()->save($form, $post, $entity);

        $cacheDriver = $this->getEntityManager()
            ->getConfiguration()
            ->getQueryCacheImpl();
        $cacheDriver->delete('lista_' . $this->getRouteName());

        if (! $savedEntity) {
            return [
                'entityForm' => $form,
                'redirect' => $redirect,
                'entity' => $entity
            ];
        }
        $entity = $savedEntity;

        if (isset($post['uploaded']) && $entity->getId() > 0 && null != $this->getImageDir()) {
            $id = $entity->getId();
            $file_name = $post['uploaded'];
            if (file_exists($this->getImageDir() . '/0/' . $file_name)) {
                if (! file_exists($this->getImageDir() . '/' . $id)) {
                    @mkdir($this->getImageDir() . '/' . $id);
                }
                if (! file_exists($this->getImageDir() . '/' . $id . '/thumbnail')) {
                    @mkdir($this->getImageDir() . '/' . $id . '/thumbnail');
                }
                @rename($this->getImageDir() . '/0/' . $file_name, $this->getImageDir() . '/' . $id . '/' . $file_name);
                @rename($this->getImageDir() . '/0/thumbnail/' . $file_name, $this->getImageDir() . '/' . $id . '/thumbnail/' . $file_name);
            }
        }

        $this->flashMessenger()->addMessage($this->getServiceLocator()
            ->get('translator')
            ->translate('Operação realizada com sucesso!'));

        return $this->redirect()->toRoute($this->getActionRoute('lista'));
    }

    protected function thumbnailOptions()
    {
        return [
            'max_width' => 80,
            'max_height' => 80
        ];
    }

    /**
     * Cria um thumbnail da imagem com o tamanho especificado em $options
     *
     * @param  string  $file
     * @param  string  $src_dir
     * @param  string  $dest_dir
     * @param  array   $options
     * @return boolean
     */
    protected function createThumbnail($file, $src_dir, $dest_dir, $options)
    {
        $file_name = $src_dir . '/' . $file;
        $new_file_path = $dest_dir . '/' . $file;
        list ($img_width, $img_height) = @getimagesize($file_name);
        if (! $img_width || ! $img_height) {
            return false;
        }
        $scale = min($options['max_width'] / $img_width, $options['max_height'] / $img_height);
        if ($scale >= 1) {
            if ($file_name !== $new_file_path) {
                return copy($file_name, $new_file_path);
            }

            return true;
        }
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($file_name);
                $write_image = 'imagejpeg';
                $image_quality = isset($options['jpeg_quality']) ? $options['jpeg_quality'] : 75;
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                $src_img = @imagecreatefromgif($file_name);
                $write_image = 'imagegif';
                $image_quality = null;
                break;
            case 'png':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($new_img, false);
                @imagesavealpha($new_img, true);
                $src_img = @imagecreatefrompng($file_name);
                $write_image = 'imagepng';
                $image_quality = isset($options['png_quality']) ? $options['png_quality'] : 9;
                break;
            default:
                $src_img = null;
        }
        $success = $src_img && @imagecopyresampled($new_img, $src_img, 0, 0, 0, 0, $new_width, $new_height, $img_width, $img_height) && $write_image($new_img, $new_file_path, $image_quality);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($src_img);
        @imagedestroy($new_img);

        return $success;
    }

    /**
     * Gerencia o upload/delete/get da imagem da entidade
     *
     * @throws \InvalidArgumentException
     */
    public function imagemAction()
    {
        $arqs = [];

        if ($this->getRequest()->isDelete()) {
            $id = $this->getEvent()
                ->getRouteMatch()
                ->getParam('id', 0);
            $file = $this->getRequest()
                ->getQuery()
                ->get('file', 0);
            $dir = getcwd() . '/' . $this->getImageDir() . '/' . $id;
            $file_path = $dir . '/' . $file;
            $thumbnail_path = $dir . '/thumbnail/' . $file;
            $ok = false;
            if ($id > 0 && file_exists($dir) && file_exists($file_path)) {
                if (@unlink($file_path)) {
                    if (file_exists($thumbnail_path)) {
                        @unlink($thumbnail_path);
                    }
                    $ok = true;
                }
            }
            header('Content-type: application/json');
            echo json_encode($ok);
            exit();
        } elseif ($this->getRequest()->isPost()) {
            $id = $this->getRequest()->getPost()->get('id', 0);
            $dir = $this->getImageDir() . '/' . $id;
            $thumbDir = $dir . '/thumbnail';
            if (! file_exists($dir)) {
                @mkdir($dir);
            }
            if (! file_exists($thumbDir)) {
                @mkdir($thumbDir);
            }
            $adapter = new \Zend\File\Transfer\Adapter\Http([
                'useByteString' => false
            ]);
            $adapter->setDestination(getcwd() . '/' . $dir);
            $files = $adapter->getFileInfo();
            foreach ($files as $file => $info) {
                $name = $adapter->getFileName($file);

                $fileclass = new \stdClass();

                if (! $adapter->receive($file)) {
                    $erros = $adapter->getErrors();
                    $fileclass->error = $erros[0];
                } else {
                    $this->createThumbnail($info['name'], getcwd() . '/' . $dir, getcwd() . '/' . $thumbDir, $this->thumbnailOptions());
                    $fileclass->name = $info['name'];
                    $fileclass->size = (int) $adapter->getFileSize($file);
                    $fileclass->type = $adapter->getMimeType($file);
                    $fileclass->deleteUrl = $this->url()->fromRoute($this->getRouteName()) . '/imagem' . $id . '?file=' . urlencode($file);
                    $fileclass->deleteType = 'DELETE';
                    // $fileclass->error = 'null';
                    $fileclass->url = $this->getRequest()->getBasePath() . '/' . $this->getImageUrl() . '/' . $id . '/' . $info['name'];
                    $fileclass->thumbnailUrl = $this->getRequest()->getBasePath() . '/' . $this->getImageUrl() . '/' . $id . '/thumbnail/' . $info['name'];
                }
                $arqs[] = $fileclass;
            }
        } else {
            $id = $this->getEvent()->getRouteMatch()->getParam('id', 0);
            $dir = $this->getImageDir() . '/' . $id;
            if ($id > 0 && file_exists($dir)) {
                $lista = scandir($dir);
                foreach ($lista as $file) {
                    $file_path = $dir . '/' . $file;
                    if (! is_file($file_path) || $file[0] == '.' || 'Thumbs.db' == $file) {
                        continue;
                    }

                    $fileclass = new \stdClass();
                    $fileclass->name = $file;
                    $fileclass->size = filesize($file_path);
                    $fileclass->deleteUrl = $this->url()->fromRoute($this->getRouteName()) .'/imagem/' . $id . '?file=' . urlencode($file);
                    $fileclass->deleteType = 'DELETE';
                    // $fileclass->error = 'null';
                    $fileclass->url = $this->getRequest()->getBasePath() . '/' . $this->getImageUrl() . '/' . $id . '/' . $file;
                    $fileclass->thumbnailUrl = $this->getRequest()->getBasePath() . '/' . $this->getImageUrl() . '/' . $id . '/thumbnail/' . $file;

                    $arqs[] = $fileclass;
                }
            }
        }
        header('Pragma: no-cache');
        header('Cache-Control: private, no-cache');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        header('Vary: Accept');
        echo json_encode($arqs);
        exit();
    }

    public function indexAction()
    {
        return new ViewModel();
    }
}
