<?php
/**
.---------------------------------------------------------------------------.
|  class Table : HTML table builder                                         |
|   Version: 1.0                                                            |
|      Date: 05.04.2019                                                     |
| ------------------------------------------------------------------------- |
| Copyright © 2019 Peter Junk                                               |
' ------------------------------------------------------------------------- '
| 1.0 : -                                                                   |
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
  
  private $colClassName = 'Col_';
  private $rowClassName = 'Row_';  

  
 /*
  * @param mixed : array, object, iterator
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
      if(is_array(reset($data))) {
        $this->data = $data;
      }
      else {
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
  * @param $data : 2 dim array, iterator or tableArray Instance
  * @return instance of tableArray
  */
  public static function create($data){
    return new static($data);
  }

 /*
  * @param string : css-class-name or attribute-list 
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
  * @param mixed : array or string
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
  * format
  */
  public function format(array $format){
    $this->formats = $format;
    return $this;    
  }
  
 /*
  * @param array : array with true/false for quote html special chars
  */
  public function quoteSpecialChars($boolArr){
    $this->quotes = $boolArr;
    return $this;
  }

 /*
  * @param string: className for Colums
  */
  public function colClassName($className){
    $this->colClassName = $className;
    return $this;
  }

 /*
  * @param string: className for rows
  */
  public function rowClassName($className){
    $this->rowClassName = $className;
    return $this;
  }

  
 /*
  * render and get HTML
  */
  public function getHtml(){
    $html = $html = "\r\n<table ".$this->attributList.">";
    
    $cn = $this->colClassName;
    $rn = $this->rowClassName;
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
    if(preg_match('~(?<!\\\)%~',$format)){
      return sprintf($format, $val);
    }
    if($val instanceof dt) {
      return $val->formatL($format);
    }
    elseif($val instanceof DateTime) {
      return $val->format($format);
    }
    $dt = date_create($val);
    return $dt !== false ? $dt->format($format) : (string)$val;
  }
  
 
  //alias htmlspecialchars UTF8
  private function quot($val) {
    return htmlspecialchars($val,ENT_QUOTES,'UTF-8',false);
  }

 /*
  * ID and NAME tokens must begin with a letter ([A-Za-z]) 
  * and may be followed by any number of letters, 
  * digits ([0-9]), hyphens ("-"), underscores ("_"), colons (":"), and periods (".").
  *
  * liefert einen gesäuberten string für HTML-Id's 
  */  
  private function cleanID($str) {
    return preg_replace_callback(
      '/^[^a-z]|[^\w-:.]/i',
      function($arr) {
        return 'x'.bin2hex($arr[0]) ;
      },
      $str
    );
  }


}
