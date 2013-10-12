<?php
namespace Phplib\Tools;

/**
 * version 1.0 author 石铮
 * version 2.0 author taoeaten
 * version 3.0 author jianxu
 */

abstract Class ParseWords {

	protected $contents = NULL;
	protected $contentsTable = NULL;
	protected $loaded = FALSE;

	protected $masklevel = 0;
	protected $maskWords = array();

	public static function getParse() {
		$class = get_called_class();
		return new $class();
	}

	protected function __construct() {}

	abstract protected function loadTable(); 
    abstract public function reload();

	public function compare($withMerge = TRUE) {
		!$this->loaded && $this->load();
		$this->getMaskWords();
		
		if ($withMerge) {
			$this->mergeMaskedString();
			$ret = array(
				'level' => $this->masklevel,
				'mergedContent' => $this->contents,
				'maskWords' => $this->maskWords,
			);
		}
		else {
			$ret = array(
				'maskWords' => $this->maskWords,
			);
		}

		return $ret;
	}

	protected function load() {
		$this->loadTable();
		$this->loaded = TRUE;
	}

	public function fill($string) {
		$this->contents = $string;
		return $this;
	}

	protected function serialization($wordsInfo) {
		$serialTable = array();
		$status_num = 0;
		foreach ($wordsInfo as $word) {
			$ptr = 0; //当前状态指针
			$word['mask_word'] = trim($word['mask_word']);
			$word['mask_word'] = strtolower($word['mask_word']);
			$length = strlen($word['mask_word']);
			for ($i = 0; $i < $length; ++$i) {
				if ((ord($word['mask_word']{$i}) & 0xf0) == 224) {
					//a chinese char contains 3 * 8bit
					$sword = $word['mask_word']{$i} . $word['mask_word']{$i+1} . $word['mask_word']{$i+2};
					$hash_num = self::hashChar($sword);
					$i += 2;
				}
				else {
					//a normal char contains 1 * 8bit
					$hash_num = self::hashChar($word['mask_word']{$i});
				} 
				if (empty($serialTable[$ptr][$hash_num])) {
					//a new char has not exist in the hashtable 
					if ($i < $length - 1) {
						//the 1st or 2nd char in a chinese word
						++$status_num;
						$serialTable[$ptr][$hash_num] = $status_num;
						$ptr = $status_num; 
					}
					else {
						//a normal char or the 3rd char in a chinese word
						$serialTable[$ptr][$hash_num] = -1 - intval($word['mask_type']);
					} 
				}
				elseif ($serialTable[$ptr][$hash_num] < 0) {
					//TODO now, the 'abcd' is not work is exist 'abc';
                    $preType = -1 - $serialTable[$ptr][$hash_num];
                    if (intval($word['mask_type']) > $preType) {
                        if ($i = $length - 1) {
                            $serialTable[$ptr][$hash_num] = -1 - intval($word['mask_type']);
                        }
                    }
					break;
				}
				else {
					$ptr = $serialTable[$ptr][$hash_num]; 
				} 
			}
		}
		return $serialTable;
	}

	//hash the character
	protected function hashChar($word) { 
		switch(strlen($word)) { 
			case 1:  //this is non-chinese character, just return the ascii code
				return ord($word);
				break;
			case 3:  //if chinese character, the hash = ((first bit)-224)*64*64+((second bit)-128)*64+(third bit) 
				$ret = ((ord($word{0}) & 0x1f) << 12) + ((ord($word{1}) & 0x7f) << 6) + (ord($word{2}) & 0x7f); 
				return $ret;
		}
	}
	
	/**
	 * @return array
	 * @access public
	 */
	public function getMaskWords() {
		$string = strtolower($this->contents);
		$length = strlen($string);
		$this->masklevel = 0;
		for ($i = 0; $i < $length; ++$i) {
			$ptr = 0;
			if ((ord($string{$i}) & 0xf0) == 224) {
				$sword = $string{$i} . $string{$i+1} . $string{$i+2};
				$hash_num = self::hashChar($sword);
				$i += 2;
				$temp = -2;
			}
			else {
				$hash_num = self::hashChar($string{$i});
				$temp = 0;
			}
			$j = $i + 1;
			while (isset($this->contentsTable[$ptr][$hash_num])) {
				$ptr = $this->contentsTable[$ptr][$hash_num];
				if ($ptr < 0) {
					$maskWord = "";
					for ($k = $i + $temp; $k < $j; ++$k) {
						$maskWord .= $string{$k};
						$this->contents{$k} = "*";
					}
					$maskType = -1 - $ptr;
					$this->maskWords[] = array(
						"mask_word" => $maskWord,
						"mask_type" => $maskType,
					);
					if (($maskType) > $this->masklevel) {
						$this->masklevel = $maskType;
					}
					break;
				}
				else {
					if ((ord($string{$j}) & 0xf0) == 224 ) {
						$sword = $string{$j} . $string{$j+1} . $string{$j+2};
						$hash_num = self::hashChar($sword);
						$j += 3;
					}
					else {
						$hash_num = self::hashChar($string{$j});
						++$j;
					}
				}
			}
		}

	}

	public function getMaskWordsNew() {
		$string = strtolower($this->contents);
		$length = strlen($string);
		$this->masklevel = 0;
		for ($i = 0; $i < $length; ++$i) {
			$ptr = 0;
			if ((ord($string{$i}) & 0xf0) == 224) {
				$sword = $string{$i} . $string{$i+1} . $string{$i+2};
				$hash_num = self::hashChar($sword);
				$i += 2;
				$temp = -2;
			}
			else {
				$hash_num = self::hashChar($string{$i});
				$temp = 0;
			}
			$j = $i + 1;
			while (isset($this->contentsTable[$ptr][$hash_num])) {
				$ptr = $this->contentsTable[$ptr][$hash_num];
				if ($ptr < 0) {
					$maskWord = "";
					$maskType = -1 - $ptr;
                    for ($k = $i + $temp; $k < $j; ++$k) {
                        $maskWord .= $string{$k};
                        if ($maskType > 1) {
                            $this->contents{$k} = "*";
                        }
                    }
					$this->maskWords[] = array(
						"mask_word" => $maskWord,
						"mask_type" => $maskType,
					);
					if (($maskType) > $this->masklevel) {
						$this->masklevel = $maskType;
					}
                    $i = $j - 1;
					break;
				}
				else {
					if ((ord($string{$j}) & 0xf0) == 224 ) {
						$sword = $string{$j} . $string{$j+1} . $string{$j+2};
						$hash_num = self::hashChar($sword);
						$j += 3;
					}
					else {
						$hash_num = self::hashChar($string{$j});
						++$j;
					}
				}
			}
		}

	}

	protected function mergeMaskedString() {
		$this->contents = preg_replace("/\*+/", "", $this->contents);
	}

}
