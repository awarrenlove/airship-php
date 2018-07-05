<p align="center">
  <a href="https://airshiphq.com/" target="_blank">
    <img  alt="Airship" src="https://avatars3.githubusercontent.com/u/29476417?s=200&v=4" class="img-responsive">
  </a>
</p>



# Airship PHP

## Requirement
PHP 5.5 or higher

## Installation
`php composer.phar require airship/airship-php`


## Usage
```php
require "vendor/autoload.php";

// Create an instance with api_key and env_key
$airship = new Airship\Airship("<api_key>", "<env_key>");
// Should be used as a singleton

// e.g.,
// $airship = new Airship\Airship("r9b72kqdh1wbzkpkf7gntwfapqoc26bl", "nxmqp35umrd3djth");

if ($airship->is_enabled("bitoin-pay", ["id" => 5])) {
  // ...
}

// Define your object
$obj = [
  "type" => "User", // "type" starts with a capital letter "[U]ser", "[H]ome", "[C]ar". If omittied, it will default to "User"
  "id" => "1234", // "id" must be a string or integer
  "display_name" => "ironman@stark.com" // must be a string. If omitted, the SDK will use the same value as "id" (converted to a string)
]

// The most compact form can be:
$obj = [
  "id" => 1234
]
// as this will translate into:
$obj = [
  "type" => "User",
  "id" => "1234",
  "display_name" => "1234"
]

$airship->is_enabled("bitcoin-pay", $obj) // Does the object have the feature "bitcoin-pay"?
$airship->get_variation("bitcoin-pay", $obj) // Get the variation associated with a multi-variate flag
$airship->is_eligible("bitcoin-pay", $obj)
// Returns true if the object can potentially receive the feature via sampling
// or is already receiving the feature.

// Note: It may take up to a minute for objects gated to show up on our web app.
```


## Attributes (for complex targeting)
```php
// Define your object with an attributes dictionary of key-value pairs.
// Values must be a string, a number, or a boolean. nil values are not accepted.
// For date or datetime string value, use iso8601 format.
$obj = [
  "type" => "User",
  "id" => "1234",
  "display_name" => "ironman@stark.com",
  "attributes" => [
    "t_shirt_size" => "M",
    "date_created" => "2018-02-18",
    "time_converted" => "2018-02-20T21:54:00.630815+00:00",
    "owns_property" => true,
    "age" => 39
  ]
]

// Now in app.airshiphq.com, you can target this particular user using its
// attributes
```

## Group (for membership-like cascading behavior)
```php
// An object can be a member of a group.
// The structure of a group object is just like that of the base object.
$obj = [
  "type" => "User",
  "id" => "1234",
  "display_name" => "ironman@stark.com",
  "attributes" => [
    "t_shirt_size" => "M",
    "date_created" => "2018-02-18",
    "time_converted" => "2018-02-20T21:54:00.630815+00:00",
    "owns_property" => true,
    "age" => 39
  ],
  "group" => [
    "type" => "Club",
    "id" => "5678",
    "display_name" => "SF Homeowners Club",
    "attributes" => [
      "founded" => "2016-01-01",
      "active" => true
    ]
  ]
]

// Inheritance of values `enabled?`, `variation`, and `eligible?` works as follows:
// 1. If the group is enabled, but the base object is not,
//    then the base object will inherit the values `enabled?`, `variation`, and
//    `eligible?` of the group object.
// 2. If the base object is explicitly blacklisted, then it will not inherit.
// 3. If the base object is not given a variation in rule-based variation assignment,
//    but the group is and both are enabled, then the base object will inherit
//    the variation of the group's.


// You can ask questions about the group directly (use the `is_group` flag):
$obj = [
  "is_group" => true,
  "type" => "Club",
  "id" => "5678",
  "display_name" => "SF Homeowners Club",
  "attributes" => [
    "founded" => "2016-01-01",
    "active" => true
  ]
]

$airship->is_enabled("bitcoin-pay", $obj)
```
___

# License
 [MIT](/LICENSE)

[![StackShare](https://img.shields.io/badge/tech-stack-0690fa.svg?style=flat)](https://stackshare.io/airship/airship)
