<?php


//get json
$request = json_decode(file_get_contents('php://input')); 
$result = null;
$report = new ReportsHandler($request);
header('Content-Type: application/json');
echo json_encode($report->getResult());
exit;
class ReportsHandler
{
	private $type = 1;
	private $result = null;
	private $dbContext = null;
	private $request = null;
	public function __construct($request)
	{
		//db
		require_once('DbHandler.php');
		$this->dbContext = (new Db())->getContext();
		$this->request = $request;
		
		$this->defineReportType($request);
	}
	
	public function getResult()
	{
		return $this->result;
	}
	
	private function defineReportType()
	{ 
		if(@$this->request->type == 1) $this->result = $this->Simple();
		
	}
	
	public function Simple()
	{
		$SMSLENGTH = 60;
		$date1 = @$this->request->date1;
		$date2 = @$this->request->date2;
		if(!$date1 || !$date2 ) return null;
		$result = null;
		$statement = "SELECT src, SUM(CEILING(LENGTH(content)/$SMSLENGTH)) as count FROM smpp where date BETWEEN '$date1' AND '$date2' GROUP BY src";
		try {
			$statement = $this->dbContext->query($statement);
			$result = $statement->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\PDOException $e) {
		    exit($e->getMessage());
		}
		return [$date1, $date2, $result];
	}
}