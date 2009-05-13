<?php

define('DEFAULT_INITIAL_DEPTH', 4);
define('DEFAULT_MASK', 0777);

class Filestorage {
	
	private $root_path;
	private $is_new;

	public function __construct($root_path, $is_new=true) {
		if (!is_string($root_path)) {
			throw new NotStringException('Root path is not a string!');
		}
		$this->root_path = $root_path;
		$this->is_new = $is_new;
		if ($this->is_new && !is_dir($this->root_path)) {
			mkdir($this->root_path, DEFAULT_MASK);
		}
	}

	public function addFile($filename=false) {
		$string_length = rand(DEFAULT_INITIAL_DEPTH, 32);
		$file_hash = substr(md5($filename . time()), 0, $string_length);
		$this->walkDirs($file_hash);
	}

	private function walkDirs($file_hash) {
		if (!$this->isHex($file_hash)) {
			throw new FileHashNotHex('File needs to be converted to hex!');
		}
		$initial_dirs = substr($file_hash, 0, DEFAULT_INITIAL_DEPTH);
		$post_dirs = substr($file_hash, DEFAULT_INITIAL_DEPTH);
		$chars = preg_split('//', $file_hash, -1, PREG_SPLIT_NO_EMPTY);
		foreach($chars as $char) {
			$full_path .= $char . '/';
		}
		$full_path = $this->root_path . $full_path;
		if (!is_dir($full_path)) {
			mkdir($full_path, DEFAULT_MASK, true);
			touch($full_path . $file_hash . '.' . 'png');
		}
	}

	private function isHex($hex) {
		return preg_match('/[a-f0-9]{4,}/', $hex);
	}

}

class NotStringException extends Exception {}
class FileHashNotHex extends Exception {}

/*
$fs = new Filestorage('images/');
$fs->addFile('test.jpg');
*/

?>
