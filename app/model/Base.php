<?php

namespace Tq\ShortVideoContest\Model;

use Tq\Com\Core\Db;

class Base
{
    /**
     * @var db Êı¾İ¿â
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
    protected function getDb($database = 'tq_app')
    {
        if (!$this->db[$database]) {
            $this->db[$database] = Db::init($database);
        }
        return $this->db[$database];
    }
}