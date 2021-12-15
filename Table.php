<?php
/**
.---------------------------------------------------------------------------.
|  class Table : HTML table builder                                         |
|   Version: 1.3                                                            |
|      Date: 2021-12-13                                                     |
| ------------------------------------------------------------------------- |
| Copyright © 2019 Peter Junk                                               |
' ------------------------------------------------------------------------- '
| PHP : >= 5.6                                                              |
' ------------------------------------------------------------------------- '
*/
class Table {

  const FIRSTLINE = 4;   //title -> first Line data
  const KEY = 8;  //title -> keys from subarray
  
  private $data;
  private $title = [];
  private $attributList = 'border = 1';
  private $quotes = [];
  private $formats = [];
  
  private $colClass = 'Col_';
  private $rowClass = 'Row_';  

  
 /*
  * @param mixed $data : array, object, iterator
  */
  public function __construct($data){
    if(is_object($data)) {
      if($data instanceof \Traversable) {
        $newData = [];
        foreach($data as $key => $row){
          $newData[$key] = $row;
        }
        $data = $newData;
      }
      else {
        $data = json_decode(json_encode($data), true); 
      }
    }
    if(is_array($data)) {
      $firstRow = reset($data);
      if(is_array($firstRow)) {
        $this->data = $data;
      }
      elseif(is_scalar($firstRow)) {
        //1 dim Array with key=>value to 2-dim-array
        $newData = [];
        foreach($data as $key => $value){
          $newData[] = [$key,$value];
        }
        $this->data = $newData;       
      }
      else {  //object
        $this->data = json_decode(json_encode($data), true); 
      }
    }
    else {
      $msg = "Parameter must cast to Array for ".__METHOD__;
      throw new \InvalidArgumentException($msg);
    }
  }
  
 /*
  * create a instance
  * @param mixed $data : 2 dim array, iterator or tableArray Instance
  * @return static instance of tableArray
  */
  public static function create($data){
    return new static($data);
  }

 /*
  * set html-Attributes 
  * @param string $list: css-class-name or attribute-list 
  * @return $this
  */
  public function attribute($list){
    if(is_string($list) AND $list != "") {
      if(stripos($list,'=')) {
        $this->attributList = $list;
      }
      else {
        //only name
        $this->attributList = 'class="'.$list.'"';
      }
    }
    return $this;
  }
  
 /*
  * set titles for columns
  * @param mixed $title
  * $title: Table::KEY, Table::FIRSTLINE, array or string with comma as delimiter
  * @return $this
  */
  public function title($title){
    if(is_string($title)) {
      $title = explode(",",$title);
    }
    elseif($title === self::KEY) {
      $title = array_keys(reset($this->data));
    }
    elseif($title === self::FIRSTLINE) {
      $title = array_shift($this->data);
    }
    
    if(!is_array($title)) {
      $msg = "Invalid Parameter for ".__METHOD__;
      throw new \InvalidArgumentException($msg);
    } 
    
    $this->title = $title;
    return $this;
  }
  
 /*
  * set format for every column
  * may use sprintf-formats and date/datetime-formats
  * @param array $format: 
  * @return $this
  */
  public function format(array $format){
    $this->formats = $format;
    return $this;    
  }
  
 /*
  * set a mask if quote html special chars
  * @param array $boolArr: array with true/false (default all true)
  * @return $this
  */
  public function quoteSpecialChars($boolArr){
    $this->quotes = $boolArr;
    return $this;
  }

 /*
  * @param string $className: className for Colums
  * @return $this
  */
  public function colClassName($className){
    $this->colClass = $className;
    return $this;
  }

 /*
  * @param string $className: className for rows
  * @return $this
  */
  public function rowClassName($className){
    $this->rowClass = $className;
    return $this;
  }

 /*
  * render and get HTML
  * @return string : html
  */
  public function getHtml(){
    $html = $html = "\r\n<table ".$this->attributList.">";
    
    $cn = $this->colClass;
    $rn = $this->rowClass;
    //thead
    if(!empty($this->title)) {
      $html .= "\r\n  <thead>\r\n    <tr>";
      foreach($this->title as $i => $title) {
        $html .= "\r\n".'      <th class="'.$this->cleanID($cn.$i).'">';
        $html .= $this->quot($title);
        $html .= '</th>'; 
      }
      $html .= "\r\n    </tr>\r\n  </thead>";
    }
    //tbody
    if(!empty($this->data)) {
      $rowIndex = 0;
      $html .= "\r\n  <tbody>";
      foreach($this->data as $k => $subArr) {
        $html .= "\r\n    ".'<tr class="'.$rn.$rowIndex.'">';
        foreach($subArr as $i => $val) {
          $html .= "\r\n      ".'<td class="'.$this->cleanID($cn.$i).'">';
          if(array_key_exists($i,$this->formats)) $val = $this->formatValue($this->formats[$i],$val);
          $html .= (isset($this->quotes[$i]) AND !$this->quotes[$i]) ? $val : $this->quot($val);
          $html .= '</td>';
        }
        $html .= "\r\n    </tr>";
        ++$rowIndex;
      }
      $html .= "\r\n  </tbody>";
    }
    
    $html .= "\r\n</table>\r\n";
    return $html;
  }
  
 /**
  * Return HTML for the table
  * @return string
  */
  public function __toString()
  {
    return $this->getHtml();
  }  
  
 /*
  * private
  */
  
  /*
   * @param string $format: formatstring for printf or date
   * @param mixed $val: int,float,string 
   * @return string: formatted var or (string)$val if error
   * 
   */
  private function formatValue($format, $val){
    if(empty($format)) return (string)$val;
    if(is_callable($format)) {
      //format contain a user-function
      return $format($val);
    }
    if(is_string($format)){
      if(preg_match('~(?<!\\\)%~',$format)){
        return sprintf($format, $val);
      }
      $dt = date_create($val);
      if($dt !== false) {
        return $dt->format($format);
      }
      return (string)$val;
    }
    if($val instanceof dt) {
      return $val->formatL($format);
    }
    elseif($val instanceof DateTime) {
      return $val->format($format);
    }
    return (string)$val;
  }
  
  //alias htmlspecialchars UTF8
  private function quot($val) {
    if(is_scalar($val) 
        OR (is_object($val) AND method_exists($val,'__toString'))
        OR $val === NULL
      ) {
      $val = (string)$val;
    }
    else {
      $val = "obj ".get_class($val)."?";
    }
    return htmlspecialchars($val,ENT_QUOTES,'UTF-8',false);
  }

 /* 
  * get a valid string may use for a id-Attribute
  * @param string $str
  * comment:
  * ID and NAME tokens must begin with a letter ([A-Za-z]) 
  * and may be followed by any number of letters, 
  * digits ([0-9]), hyphens ("-"), underscores ("_"), colons (":"), and periods (".").
  */  
  private function cleanID($str) {
    return preg_replace_callback(
      '/^[^a-z]|[^\w\-:.]/i',
      function($arr) {
        return 'x'.bin2hex($arr[0]) ;
      },
      $str
    );
  }

}
