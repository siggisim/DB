DB
====

DB is the missing database helper class for PHP.

DB allows you to insert, select, update, and delete from a mysql database using arrays.

Super lightweight. Super easy.

Quick Start
-----------
Start using DB in three easy steps:

* Download the DB.class.php and DB.config.php
* Modify DB.config.php to connect to your MySQL server
* Include DB.config.php in your php file

You now have the $db object you can use to start talking to your database.

Usage
-----------

__Simple Insert__
```php
$db->insertRow("cats", array("name"=>"Fluffy", "breed"=>"Siamese", "age"=>2));
$db->insertRow("cats", array("name"=>"Charlie", "breed"=>"Siamese", "age"=>3));
```

__Simple Select Single Row__
```php
$cat = $db->selectRow("cats", array("name"=>"Fluffy"));
echo $cat["age"];
```

__Simple Select Multiple Rows__
```php
$cats = $db->selectRows("cats", array("breed"=>"Siamese"));
foreach ($cats as $cat)
  echo $cat["name"];
```

__Simple Update Row__
```php
$cats = $db->updateRow("cats", array("breed"=>"Bengal"), array("name"=>"Fluffy"));
```

__Simple Delete Row__
```php
$cats = $db->deleteRow("cats", array("name"=>"Fluffy"));
```

__Increment Field__
```php
$db->incrementValue("cats", "age", array("name"=>"Fluffy"));
```

__Sum Rows__
```php
$sum = $db->sumRows("cats", "age", array());
echo "Total cat years: " . $sum;
```

__Search Rows__
```php
$cats = $db->searchRows("cats", array("name"=>"Fluf"));
```

__Select, Modify, Update__
```php
$cat = $db->selectRow("cats", array("name"=>"Fluffy"));
$cat['age'] = 3;
$db->updateRow("cats", array("name"=>"Fluffy"), $cat);
```

__Advanced Select__
```php
$db->select("cats", array("breed"=>"Siamese"), "age", 2);
```
