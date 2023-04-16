# HTML table builder 

A PHP class for building an HTML table with data from a 2 dimensional array

### Features

- Use Table-Values from Arrays, Objects how PDO-Statement and Iterators
- Add Title from Array, first Data Line or Array-Keys
- Add Attributes how name, css-class for styling 
- Handles context switching for HTML
- Formatting values with sprintf-Formats or date-Formats
- One class and One File

### Usage

```php
require '/yourpath/Table.php';  //or autoload

$data = [  
  ['name' => 'A1', 'value' => 3],  
  ['name' => 'A12', 'value' => 6],  
  ['name' => 'A2','value' => 14],  
  ['name' => 'A14','value' => 7],  
]; 
$table = Table::create($data) 
  ->title(['Name','Wert']) 
  ->getHtml() 
; 
//Output
echo $table;
```

### Demo and Test

http://jspit.de/check/phpcheck.table.php

### Requirements

- PHP 5.6 .. 8.2

## Class-Info

| Info | Value |
| :--- | :---- |
| Declaration | class Table |
| Datei | Table.php |
| Date/Time modify File | 2023-04-16 11:15:58 |
| File-Size | 7452 Byte |
| MD5 File | d531c208b9a7bf2fb554d6e204c7bf18 |
| Version | 1.4 |
| Date | 2023-04-16 |

## Public Methods

| Methods and Parameter | Description/Comments |
| :-------------------- | :------------------- |
| final public function __construct($data) | @param mixed $data : array, object, iterator |
| public static function create($data) | create a instance<br>@param mixed $data : 2 dim array, iterator or tableArray Instance<br>@return static instance of tableArray |
| public function attribute($list) | set html-Attributes <br>@param string $list: css-class-name or attribute-list <br>@return $this |
| public function title($title) | set titles for columns<br>@param mixed $title<br>$title: Table::KEY, Table::FIRSTLINE, array or string with comma as delimiter<br>@return $this |
| public function format(array $format) | set format for every column<br>may use sprintf-formats and date/datetime-formats<br>@param array $format: <br>@return $this |
| public function quoteSpecialChars($boolArr) | set a mask if quote html special chars<br>@param array $boolArr: array with true/false (default all true)<br>@return $this |
| public function colClassName($className) | @param string $className: className for Colums<br>@return $this |
| public function rowClassName($className) | @param string $className: className for rows<br>@return $this |
| public function getHtml() | render and get HTML<br>@return string : html |
| public function __toString() | Return HTML for the table<br>@return string |

## Constants

| Declaration/Name | Value | Description/Comments |
| :--------------- | :---- | :------------------- |
|  const FIRSTLINE = 4; //title -&gt; first Line data | 4 |  title -&gt; first Line data  |
|  const KEY = 8; //title -&gt; keys from subarray | 8 |  title -&gt; keys from subarray  |
