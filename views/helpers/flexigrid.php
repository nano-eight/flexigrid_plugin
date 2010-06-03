<?php
class FlexigridHelper extends AppHelper {

	function initialize(&$controller, $settings = array()) {
		$this->Controller = $controller;
		$this->_set($settings);
	}
	
	function dialog($options = array()) {
		$default = array(
			'success' => 'The data has been saved.',
			'error' => 'The data could not be saved. Please try agein.',
		);
		$options = array_merge($default, $options);

$out = "
$('input[type=\"submit\"]').click(function(){
	$(this).parents('form:first').ajaxSubmit({
		success: function(responseText, responseCode) {
			if (responseText==\"success\") {
				$.jGrowl('".$options['success']."', {header:'add result', theme:'jgSuccess'});
				$('#dialog').dialog('close');
				$('#".$this->Controller->name."').flexReload();
			} else {
				$.jGrowl('".$options['error']."', {header:'add result', theme:'jgError'});
				$('#dialog').html(responseText);
			}
			return false;
		}
	});
	return false;
});";
		return $out;
	}

}
?>
