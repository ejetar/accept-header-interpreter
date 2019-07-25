# Accept Header Interpreter

## Table of Contents
- [About](#about)
- [Features](#features)
- [Installation](#installation)
- [Examples](#examples)
  * [First Example: Entering *valid* content](#first-example--entering--valid--content)
  * [Second Example: Entering *invalid* content](#second-example--entering--invalid--content)
  * [Third Example: Priority](#third-example--priority)
  * [Fourth Example: Displaying the original content of the Accept Header](#fourth-example--displaying-the-original-content-of-the-accept-header)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [License](#license)

## About
Accept Header toolbox 🧰 to **Laravel**. Validation and interpretation based on **RFC 7231, section 5.3.1 and 5.3.2**. Conversion of the list of media types to ordered Laravel Collections (by priority, according to RFC).

## Features
* Validation: Validates if Accept Header content is valid, according to specification;
* Conversion: Converts the list of media types to a Laravel Collection (automatically sorted by priority according to specification);

## Installation
`composer require ejetar/accept-header-interpreter`

## Examples
### First Example: Entering *valid* content
```php
use Ejetar\AcceptHeaderInterpreter\AcceptHeaderInterpreter;

try {
  $content = request()->headers->get('Accept'); //return accept header content
  //let's assume that $content is now: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8
  
  $acceptHeaderInterpreter = new AcceptHeaderInterpreter($content);
  dd($acceptHeaderInterpreter->toCollection());
  
} catch (\Exception $ex) {
  echo $ex->getMessage();
}

/* the code above will print
Collection {#756 ▼
  #items: array:6 [▼
    0 => array:3 [▼
      "type" => "text"
      "subtype" => "html"
      "parameters" => array:1 [▼
        "q" => 1
      ]
    ]
    1 => array:3 [▼
      "type" => "application"
      "subtype" => "xhtml+xml"
      "parameters" => array:1 [▼
        "q" => 1
      ]
    ]
    3 => array:3 [▼
      "type" => "image"
      "subtype" => "webp"
      "parameters" => array:1 [▼
        "q" => 1
      ]
    ]
    4 => array:3 [▼
      "type" => "image"
      "subtype" => "apng"
      "parameters" => array:1 [▼
        "q" => 1
      ]
    ]
    2 => array:3 [▼
      "type" => "application"
      "subtype" => "xml"
      "parameters" => array:1 [▼
        "q" => "0.9"
      ]
    ]
    5 => array:3 [▼
      "type" => "*"
      "subtype" => "*"
      "parameters" => array:1 [▼
        "q" => "0.8"
      ]
    ]
  ]
}

REALIZE THAT THE ORDER OF MEDIA TYPES HAS BEEN MODIFIED
*/
```

### Second Example: Entering *invalid* content
```php
use Ejetar\AcceptHeaderInterpreter\AcceptHeaderInterpreter;

try {
  $content = request()->headers->get('Accept'); //return accept header content
  //let's assume that $content is now: text/html,application/xhtml+xml,application/xml;q=1.1,image/webp,image/apng,*/*;q=0.8
  //It is only allowed to inform values from 0 to 1 for the parameter Q, that is, the contents of this Accept Header is invalid
  
  $acceptHeaderInterpreter = new AcceptHeaderInterpreter($content);
  dd($acceptHeaderInterpreter->toCollection());
  
} catch (\Exception $ex) {
  echo $ex->getMessage();
}

/* the code above will print
Accept header value is invalid!
*/
```

### Third Example: Priority
```php
use Ejetar\AcceptHeaderInterpreter\AcceptHeaderInterpreter;

try {
  $content = request()->headers->get('Accept'); //return accept header content
  //let's assume that $content is now: */*, application/*, text/html, application/xhtml+xml
  
  $acceptHeaderInterpreter = new AcceptHeaderInterpreter($content);
  dd($acceptHeaderInterpreter->toCollection());
  
} catch (\Exception $ex) {
  echo $ex->getMessage();
}

/* the code above will print
Collection {#756 ▼
  #items: array:4 [▼
    2 => array:3 [▼
      "type" => " text"
      "subtype" => "html"
      "parameters" => array:1 [▼
        "q" => 1
      ]
    ]
    3 => array:3 [▼
      "type" => " application"
      "subtype" => "xhtml+xml"
      "parameters" => array:1 [▼
        "q" => 1
      ]
    ]
    1 => array:3 [▼
      "type" => " application"
      "subtype" => "*"
      "parameters" => array:1 [▼
        "q" => 1
      ]
    ]
    0 => array:3 [▼
      "type" => "*"
      "subtype" => "*"
      "parameters" => array:1 [▼
        "q" => 1
      ]
    ]
  ]
}

NOTE THAT ORDER OF THE MEDIA TYPES IS NO LONGER THE SAME, IT WAS MODIFIED AS A PRIORITY, ACCORDING TO THE SPECIFICATION.
*/
```

### Fourth Example: Displaying the original content of the Accept Header 
```php
use Ejetar\AcceptHeaderInterpreter\AcceptHeaderInterpreter;

try {
  $content = request()->headers->get('Accept'); //return accept header content
  //let's assume that $content is now: application/json
  
  $acceptHeaderInterpreter = new AcceptHeaderInterpreter($content);
  echo $acceptHeaderInterpreter->getOriginalContent();
  
} catch (\Exception $ex) {
  echo $ex->getMessage();
}

/* the code above will print
application/json
*/
```

## Changelog
Nothing for now...

## Contributing
Contribute to this wonderful project, it will be a pleasure to have you with us. Let's help the free software community. You are invited to incorporate new features, make corrections, report bugs, and any other form of support.
Don't forget to star in this repository! 😀 

## License
This library is a open-source software licensed under the MIT license.
