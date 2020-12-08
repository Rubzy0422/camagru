<?php
class Template
{
	function get_contents($templateName, $variables) {
		$template = file_get_contents($templateName);
		
		foreach($variables as $key => $value)
		{
			$template = str_replace('{{ '.$key.' }}', $value, $template);
		}
		return $template;
	}
}
?>
