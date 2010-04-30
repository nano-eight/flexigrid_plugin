<?php
class FlexigridComponent extends Object {
	var $name = 'FlexigridComponent';
	
	function initialize(&$controller, $settings = array()) {
		$this->Controller = $controller;
		$this->_set($settings);
	}
	
	function create($options) {
		
		// default column setting
		$default_column = array(
			'display' => 'id',
			'name'    => 'id',
			'width'   => '100',
			'sortable' => 'true',
			'align'    => 'center'
		);
		if (isset($options['fields'])) {
			foreach ($options['fields'] as $field) {
				$column = array(
					'display' => $field,
					'name' => $field,
				);
				$columns[] = array_merge($default_column, $column);
			}
		}
		if (isset($options['columns'])) {
			foreach ($options['columns'] as $key => $column){
				$options['columns'][$key] = array_merge($default_column, $column);
			}
			$options['columns'] = array_merge($options['columns'], $columns);
		}else{
			$options['columns'] = $columns;
		}

		// default buttn setting
		$default_buttons = array(
			array(
				'name' => 'Add',
				'cssclass' => 'add',
				'onpress' => 'button',
			),
			array(
				'name' => 'Delete',
				'cssclass' => 'delete',
				'onpress' => 'button',
			),
			array(
				'name' => 'Edit',
				'cssclass' => 'edit',
				'onpress' => 'button',
			),
			array('name' => 'separator'),
		);
		if (!isset($options['buttons'])) {
			$options['buttons'] = $default_buttons;
		}
		
		// default searchitems setting
		if (!isset($options['searchitems'])) {
			$options['searchitems'] = array(
				array(
					'name' => 'username',
					'display' => 'username',
					'isdefault' => 'search text',
				),
			);
		}
		
		// other default setting
		$default_options = array(
			'id' => $this->Controller->name,
			'title' => $this->Controller->name,
			'url' => $this->Controller->webroot.'flexigrid/xml/index/'.$this->Controller->name.'.xml',
			'width' => 550,
			'height' => 200,
			'sortby' => 'id',
			'sortorder' => 'asc',
		);
		$options = array_merge($default_options,$options);
		
		$flexigrid =
		"$(document).ready(function(){
		
		// default dialog setting
		var dialogOpts = {
			modal: true,
			autoOpen: false,
			height: 320,
			width: 220,
			buttons: {},
		};
		$(\"#dialog\").dialog(dialogOpts);    //end dialog setting
		
		$(\"#".$options['id']."\").flexigrid
			(
			{
			url: '".$options['url']."',
			dataType: 'xml',
			colModel : [";
		
		// set columns parameter
		foreach ($options['columns'] as $column) {
			$flexigrid .= "{display: '".$column['display']."', name : '".$column['name']."', width : ".$column['width'].", sortable : ".$column['sortable'].", align: '".$column['align']."'},";
		}
		
		// delete last ,
		$flexigrid = substr($flexigrid, 0, -1);
		$flexigrid .= "],
		buttons : [";
		
		// set buttons
		foreach ($options['buttons'] as $button) {
			if ($button['name'] == 'separator') {
				$flexigrid .= "{separator: true},";
			} else {
				$flexigrid .= "{name: '".$button['name']."', bclass: '".$button['cssclass']."', onpress : ".$button['onpress']."},";
			}
		}
		
		// delete last ,
		$flexigrid = substr($flexigrid, 0, -1);
		
		$flexigrid .= "
			],
			searchitems : [";
		
		// set search items
		foreach ($options['searchitems'] as $searchitem) {
			$flexigrid .= "{display: '".$searchitem['display']."', name : '".$searchitem['name']."'},";
		}
		
		$flexigrid .= "    ],
			sortname: \"".$options['sortby']."\",
			sortorder: \"".$options['sortorder']."\",
			usepager: true,
			title: '".$options['title']."',
			useRp: true,
			rp: 10,
			showTableToggleBtn: true,
			width: ".$options['width'].",
			height: ".$options['height']."
			}
			);
		});
		function button(com,grid){
			if (com=='Delete'){
				if($('.trSelected',grid).length>0){
					if(confirm('Delete ' + $('.trSelected',grid).length + ' items?')){
						var items = $('.trSelected',grid);
						var itemlist ='';
						for(i=0;i<items.length;i++){
							itemlist+= items[i].id.substr(3)+\",\";
						}
						$.ajax({
							type: \"POST\",
							dataType: \"json\",
							url: \"".$this->Controller->name."/delete\",
							data: \"items=\"+itemlist,
							success: function(data){
								if (data.status=='success') {
									$.jGrowl(data.message, {header:'delete result', theme:'jgSuccess'});
								} else if (data.status=='error') {
									$.jGrowl(data.message, {header:'delete result', theme:'jgError'});
								} else {
									$.jGrowl(data.message, {header:'delete result'});
								}
								$(\"#".$this->Controller->name."\").flexReload();
							}
						});
					}
				} else {
					return false;
				}
			} else if (com=='Add') {
				var title = {title:'Add'};
				$('#dialog').dialog(title);
				$('#dialog').load('".$this->Controller->name."/add').dialog('open');
				return false;
			} else if (com=='Edit') {
				if($('.trSelected',grid).length>0){
					var items = $('.trSelected',grid);
					item = items[0].id.substr(3);
					var title = {title:'Edit'};
					$('#dialog').dialog(title);
					$('#dialog').load('".$this->Controller->name."/edit/'+item).dialog('open');
				}
			}
		}

		flexcolumns = '";
		
		foreach ($options['columns'] as $column) {
			$flexigrid .= $column['name'].', ';
		}
		
		// delete last ,
		$flexigrid = substr($flexigrid, 0, -2);
		$flexigrid .= "';";

		// return flexigrid script source
		return $flexigrid;
	}

}
?>
