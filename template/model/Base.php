<?php

namespace Tq\${app_name}\Model;

use Tq\Com\Core\Db;

class Base
{
    /**
     * @var db 数据库
     */
    private $db = [];

    /**
     * Base constructor.
     */
    public function __construct()
    {
        # code here
    }

    /**
     * @param  string  $database
     * @return Db
     */
    protected function getDb($database = '${default_db}')
    {
        if (!$this->db[$database]) {
            $this->db[$database] = Db::init($database);
        }
        return $this->db[$database];
    }
}