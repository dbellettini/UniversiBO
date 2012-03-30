<?php
namespace UniversiBO\Legacy\Framework;

use \DB;
use \Error;

class DBConnectionFactory
{
    private $dsn = array();
    private $connections = array();

    public function registerDSN($id, $dsn)
    {
        $this->dsn[$id] = $dsn;
    }

    public function getConnection($id)
    {
        if(array_key_exists($id, $this->connections)) {
            return $this->connections[$id];
        }

        if(!array_key_exists($id, $this->dsn)) {
            throw new \InvalidArgumentException('Invalid dsn');
        }

        return $this->createConnection($id);
    }

    private function createConnection($id) {
        $conn = DB::connect($this->dsn[$id]);
        
        if(DB::isError($conn)) {
            Error::throwError(_ERROR_CRITICAL,array('msg'=>\DB::errorMessage($conn),'file'=>__FILE__,'line'=>__LINE__));
        }

        return $conn;
    }
}