<?php
namespace LosBase\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\ColorInterface as Color;
use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;
use Zend\View\Model\ConsoleModel;
use Zend\Code\Generator\ValueGenerator;

/**
 * @codeCoverageIgnore
 */
class CreateController extends AbstractActionController
{
    public function crudAction()
    {
        $console = $this->getServiceLocator()->get('console');
        $tmpDir = sys_get_temp_dir();
        $request = $this->getRequest();
        $name = $request->getParam('name');
        $path = rtrim($request->getParam('path'), '/');
        if (empty($path)) {
            $path = '.';
        }
        if (! file_exists("$path/module") || ! file_exists("$path/config/application.config.php")) {
            return $this->sendError("The path $path doesn't contain a ZF2 application. I cannot create a module here.");
        }
        if (file_exists("$path/module/$name")) {
            return $this->sendError("The module $name already exists.");
        }
        $filter = new CamelCaseToDashFilter();
        $viewfolder = strtolower($filter->filter($name));
        $name = ucfirst($name);

        mkdir("$path/module/$name/config", 0777, true);
        mkdir("$path/module/$name/src/$name/Controller", 0777, true);
        mkdir("$path/module/$name/src/$name/Entity", 0777, true);
        mkdir("$path/module/$name/src/$name/Service", 0777, true);
        mkdir("$path/module/$name/view/$viewfolder/crud", 0777, true);

        $crudDir = __DIR__.'/../../../data/crud';
        $files = [
            "config/module.config.php" => "config/module.config.php",
            "autoload_classmap.php" => "autoload_classmap.php",
            "autoload_function.php" => "autoload_function.php",
            "autoload_register.php" => "autoload_register.php",
            "Module.php" => "Module.php",
            "src/$name/Controller/CrudController.php" => "src/LosCrudModule/Controller/CrudController.php",
            "src/$name/Entity/$name.php" => "src/LosCrudModule/Entity/Entity.php",
            "src/$name/Module.php" => "src/LosCrudModule/Module.php",
            "src/$name/Service/$name.php" => "src/LosCrudModule/Service/Entity.php",
            "view/$viewfolder/crud/add.phtml" => "view/los-crud-module/crud/add.phtml",
            "view/$viewfolder/crud/delete.phtml" => "view/los-crud-module/crud/delete.phtml",
            "view/$viewfolder/crud/edit.phtml" => "view/los-crud-module/crud/edit.phtml",
            "view/$viewfolder/crud/list.phtml" => "view/los-crud-module/crud/list.phtml",
        ];

        foreach ($files as $destFile => $origFile) {
            \file_put_contents("$path/module/$name/$destFile", $this->handleFile($crudDir."/$origFile", $name, $viewfolder));
        }

        // Add the module in application.config.php
        $application = require "$path/config/application.config.php";
        if (! in_array($name, $application['modules'])) {
            $application['modules'][] = $name;
            copy("$path/config/application.config.php", "$path/config/application.config.old");
            $content = <<<EOD
<?php
/**
 * Configuration file changed by LosBase
 * The previous configuration file is stored in application.config.old
 */

EOD;
            $generator = new ValueGenerator();
            $generator->setValue($application);
            $content .= 'return '.$generator.";\n";
            file_put_contents("$path/config/application.config.php", $content);
        }
        if ($path === '.') {
            $console->writeLine("The module $name has been created", Color::GREEN);
        } else {
            $console->writeLine("The module $name has been created in $path", Color::GREEN);
        }
    }

    private function handleFile($fileName, $moduleName, $moduleDashedName)
    {
        $file = \file_get_contents($fileName);
        $out = \str_replace('__MODULEDASHEDNAME__', $moduleDashedName, $file);
        $out = \str_replace('__MODULENAME__', $moduleName, $out);

        return $out;
    }

    protected function sendError($msg)
    {
        $m = new ConsoleModel();
        $m->setErrorLevel(2);
        $m->setResult($msg.PHP_EOL);

        return $m;
    }
}
