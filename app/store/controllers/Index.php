<?php
use Hook\Sql\Init;
use Hook\Db\{PdoConnect, Table};

class IndexController extends InitController
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_view->assign(
            ['test' => 'Login Out']
       );
    }

    public function listAction()
    {
        $this->_view->assign(
            ['test' => 'List']
        );
    }

    public function cliAction()
    {
        $this->getRequest()->isCli() || exit();

        $data = '';
        $tables = PdoConnect::getInstance()->fetchAll(Init::SQL_ALL_TABLES, [], PDO::FETCH_NUM);
        foreach ($tables as list($tableName)) {
            $table = new Table($tableName);
            $data .= '['.$tableName.']'.PHP_EOL;
            foreach ($table->field as $field) {
                $data .= $field['Field'].'.type='.substr($field['Type'], 0, strpos($field['Type'], '(')).PHP_EOL;
                $data .= $field['Field'].'.null='.$field['Null'].PHP_EOL;
                $data .= $field['Field'].'.key='.$field['Key'].PHP_EOL;
                $data .= $field['Field'].'.default='.$field['Default'].PHP_EOL;
                $data .= $field['Field'].'.extra='.$field['Extra'].PHP_EOL;

                $validate = $table->validate($field['Field']);
                $data .= $field['Field'].'.min='.$validate['min'].PHP_EOL;
                $data .= $field['Field'].'.max='.$validate['max'].PHP_EOL;
            }
            $data .= PHP_EOL;
        }

        file_put_contents(APP_ROOT.'/conf/'.APP_NAME.'_table.ini', $data);
        shell_exec('sudo service php7.2-fpm restart');
    }
}