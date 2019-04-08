# HTML table builder 

A PHP class for building an HTML table with data from a 2 dimensional array

### Features

- Use Table-Values from Arrays, Objects how PDO-Statement and Iterators
- Add Title from Array, first Data Line or Array-Keys
- Add Attributes how name, css-class for styling 
- Handles context switching for HTML
- Formatting table values
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

- PHP 5.6+, 7.x+
