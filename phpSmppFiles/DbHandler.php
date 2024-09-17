<?php
class Db
{
	private $context;
	
	private $host = 'localhost';
    private $port = '3306';
    private $db   = 'smpp';
    private $user = 'root';
    private $pass = 'MofgQXjj';
	public function __construct()
	{
		if($this->context == null)
		{
			try {
            $this->context = new \PDO(
                "mysql:host=$this->host;port=$this->port;charset=utf8mb4;dbname=$this->db",
                $this->user,
                $this->pass
            );
          } catch (\PDOException $e) {
              exit($e->getMessage());
          }
		}
	}
	
	public function getContext()
	{
		return $this->context;
	}
}



?>