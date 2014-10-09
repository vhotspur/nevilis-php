<?php

class emptyglass_transformer {
	private $unprocessed_input;
	private $processed_tokens;
	private $output;
	private $url_func;
	
	private $tokens_def = array(
		"variable" => array(
			"regexp" => '\{(\$[^}\/\|]+)(\/(h))?(\|([^}]*))?\}',
			"top" => true,
		),
		"url" => array(
			"regexp" => '%url[(]([^\)]+)[)]%',
			"top" => true,	
		),
		"foreach" => array(
			"regexp" => '%foreach[ \t]+(\$[^ \t]+)[ \t]+(\$[^%]+)%',
			"top" => true,
		),
		"before" => array(
			"regexp" => '%before%'
		),
		"after" => array(
			"regexp" => '%after%'
		),
		"body" => array(
			"regexp" => '%body%'
		),
		"foreach_end" => array(
			"regexp" => '%endforeach%'
		),
		"if" => array(
			"regexp" => '%if ([^%]+)%',
			"top" => true,
		),
		"else" => array(
			"regexp" => '%else%'		
		),
		"endif" => array(
			"regexp" => '%endif%'
		),
		"translate" => array(
			"regexp" => '%_(.*)_%',
			"top" => true,
		),
		"checked" => array(
			"regexp" => '%checked ([^%]+)%',
			"top" => true
		),
		"comment" => array(
			"regexp" => '%comment [^%]*%',
			"top" => true,
		),
		"text" => array(
			"skip" => true,
			"top" => true
		)
	);
	
	public function __construct($input) {
		$this->unprocessed_input = $input;
		$this->processed_tokens = array();
		$this->output = array("");
		$this->url_func = '';
	}
	
	public function set_url_function($name) {
		$this->url_func = $name;
	}
	
	public function make_php() {
		$this->process_main();
		return $this->output[0];
	}
	
	private function process_main() {
		while (true) {
			$token = $this->peek_next_token();
			if (isset($this->tokens_def[$token]["top"]) && $this->tokens_def[$token]["top"]) {
				call_user_func(array($this, 'process_' . $token));
			} else {
				return;
			}
		}
	}
	
	private function process_text() {
		$text = $this->expect_token("text");
		$this->add_output($text["value"]);
	}
	
	private function process_comment() {
		$this->expect_token("comment");
	}
	
	private function process_variable() {
		$variable = $this->expect_token("variable");
		$name = $variable["parts"][0];
		$mods = isset($variable["parts"][2]) ? $variable["parts"][2] : "";
		if (isset($variable["parts"][4])) {
			$name = sprintf('(@isset(%s) ? %s : "%s")', $name, $name, addslashes($variable["parts"][4]));
		}
		
		if ($mods == "h") {
			$this->add_php_code('echo htmlspecialchars(%s);', $name);
		} else {
			$this->add_php_code('echo %s;', $name);
		}
	}
	
	private function process_translate() {
		$text = $this->expect_token("translate");
		$this->add_php_code('echo _("%s");', addslashes($text["parts"][0]));
	}
	
	private function process_url() {
		$url = $this->expect_token("url");
		$this->add_php_code('echo %s(%s);', $this->url_func, $url["parts"][0]);
	}
	
	private function process_checked() {
		$checked = $this->expect_token("checked");
		$this->add_php_code('if (%s) { echo "checked=\"checked\""; }', $checked["parts"][0]);
	}
	
	private function process_if() {
		$start = $this->expect_token("if");
		$condition = $start["parts"][0];
		
		$on_then = $this->buffer_main();
		
		$on_else = "";
		
		$next = $this->expect_token_any_of(array("endif", "else"));
		if ($next["token"] == "else") {
			$on_else = $this->buffer_main();
			$next = $this->expect_token("endif");
		}
		
		$this->add_php_code('if (%s) {', $condition);
		$this->add_output($on_then);
		if ($on_else != "") {
			$this->add_php_code('} else {');
			$this->add_output($on_else);
		}
		$this->add_php_code('}');
	}
	
	private function process_foreach() {
		$start = $this->expect_token("foreach");
		$data_var = $start["parts"][0];
		$iterator_var = $start["parts"][1];
		
		$before = "";
		$after = "";
		$on_else = "";
		
		$body = $this->buffer_main();
		
		while (true) {
			$next = $this->expect_token_any_of(array("foreach_end", "body", "before", "after", "else"));
			if ($next["token"] == "foreach_end") {
				break;
			}			
			
			$text = $this->buffer_main();
			
			switch ($next["token"]) {
				case "body":
					$body .= $text;
					break;
				case "before":
					$before .= $text;
					break;
				case "after":
					$after .= $text;
					break;
				case "else":
					$on_else .= $text;
					break;
			}
		}
		
		$this->add_php_code('if (count(%s) > 0) {', $data_var);
		$this->add_output($before);
		$this->add_php_code('foreach (%s as %s) {', $data_var, $iterator_var);
		$this->add_output($body);
		$this->add_php_code('}');
		$this->add_output($after);
		if ($on_else != "") {
			$this->add_php_code('} else {');
			$this->add_output($on_else);
		}
		$this->add_php_code('}');
	}
	
	private function buffer_main() {
		$this->buffer_output();
		$this->process_main();
		return $this->get_buffered_output();
	}
	
	private function buffer_output() {
		$this->output[] = "";
	}
	
	private function get_buffered_output() {
		return array_pop($this->output);
	}
	
	private function add_php_code() {
		$argv = func_get_args();
		$format = array_shift($argv);
		
		$this->add_output_internal(vsprintf('<' . '?php ' . $format . ' ?' . '>', $argv));
	}
	
	private function add_output($str) {
		$this->add_output_internal($str);
	}
	
	private function add_output_internal($str) {
		// printf("%2d: Adding \"%s\"...\n", count($this->output), str_replace("\n", "\\n", $str));
		$this->output[count($this->output) - 1] .= $str;
	}
	
	private function expect_token($type) {
		return $this->expect_token_any_of(array($type));
	}
	
	private function expect_token_any_of($types) {
		$next = $this->peek_next_token();
		if (array_search($next, $types) === FALSE) {
			$error = sprintf('Expected %s!\n', implode(' or ', $types));
			throw new Exception($error);
		}
		return $this->next_token();
	}
	
	private function next_token() {
		if (count($this->processed_tokens) == 0) {
			$this->parse_next_token();
		}
		return array_shift($this->processed_tokens);
	}
	
	private function peek_next_token() {
		if (count($this->processed_tokens) == 0) {
			$this->parse_next_token();
		}
		return $this->processed_tokens[0]["token"];
	}
	
	private function add_parsed_token($type, $value, $parts = array()) {
		// printf("Parsed %s (%s).\n", $type, str_replace("\n", "\\n", $value));
		$this->processed_tokens[] = array(
			"token" => $type,
			"value" => $value,
			"parts" => $parts
		);
	}
	
	private function parse_next_token() {
		if ($this->unprocessed_input == "") {
			$this->add_parsed_token("eof", "");
			return;	
		}
		
		$text = "";
		while ($this->unprocessed_input != "") {
			foreach ($this->tokens_def as $id => $details) {
				if (isset($details['skip']) && $details['skip']) {
					continue;
				}
				$matches = array();
				if (preg_match('/^' . $details["regexp"] . '/', $this->unprocessed_input, $matches)) {
					if ($text != "") {
						$this->add_parsed_token("text", $text);
						return;
					}
		
					$this->unprocessed_input = substr($this->unprocessed_input, strlen($matches[0]));
					$this->add_parsed_token($id, $matches[0], array_slice($matches, 1));
					return;
				}
			}
		
			$text .= substr($this->unprocessed_input, 0, 1);
			$this->unprocessed_input = substr($this->unprocessed_input, 1);
		}
		
		$this->add_parsed_token("text", $text);
	}
}
