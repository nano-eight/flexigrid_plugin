<?php
class XmlController extends FlexigridAppController {
	var $uses = null;
	var $name = 'Xml';
	var $helpers = array('Html', 'Form');
	var $components = array('RequestHandler');
	
	function index($table) {
		
		$table = Inflector::classify($table);
		$this->loadModel($table);
		
		// set post data
		$page = $this->params['form']['page'];
		$rp	= $this->params['form']['rp'];
		$sortname = $this->params['form']['sortname'];
		$sortorder = $this->params['form']['sortorder'];
		$columns = explode(',', $this->params['form']['columns']);
		
		// set sort parameter
		if (!$sortname) {
			$sortname = 'id';
		}
		if (!$sortorder) {
			$sortorder = 'desc';
		}
		//$sort = "$sortname $sortorder";
		$sort = $sortname.' '.$sortorder;
		
		// set search parameter
		if(!empty($_POST['query'])) {
			$conditions = array($_POST['qtype'].' LIKE' => '%'.$_POST['query'].'%');
		} else {
			$conditions = '';
		}
		
		$start = (($page-1) * $rp);
		$limit = "$start, $rp";
		
		// get records
		$params = array(
			'fields' => $columns,
			'order' => $sort,
			'conditions' => $conditions,
			'limit' => $limit,
			);
		$results = $this->{$table}->find('all', $params);

		// get column name
		$columns = array_keys($results[0][$table]);
		
		// get records count
		//$total = $this->{$table}->find('count');
		$params= array(
			'fields' => $columns[0],
			'limit' => null,
		);
		$total = $this->{$table}->find('count', $params);
		
		$xml = "<rows>";
		$xml .= "<page>$page</page>";
		$xml .= "<total>$total</total>";
		
		// create xml data
		foreach ($results as $result) {
			$xml .= "<row id='".$result[$table]['id']."'>";
			foreach ($columns as $column) {
				$xml .= "<cell><![CDATA[".utf8_encode($result[$table][$column])."]]></cell>";
			}
			$xml .= "</row>";
		}
		$xml .= "</rows>";
		
		// set $xml to ctp
		$this->set('xml',$xml);
	}
}
?>
