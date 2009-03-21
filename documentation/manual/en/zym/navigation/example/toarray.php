<?php
$container = new Zym_Navigation(array(
    array(
        'label' => 'Page 1',
        'uri'   => '#'
    ),
    array(
        'label' => 'Page 2',
        'uri'   => '#',
        'pages' => array(
            array(
                'label' => 'Page 2.1',
                'uri'   => '#'
            ),
            array(
                'label' => 'Page 2.2',
               'uri'   => '#'
            )
        )
    )
));

var_dump($container->toArray());

/* Output:
array(2) {
  [0]=> array(15) {
    ["label"]=> string(6) "Page 1"
    ["id"]=> NULL
    ["class"]=> NULL
    ["title"]=> NULL
    ["target"]=> NULL
    ["rel"]=> array(0) {
    }
    ["rev"]=> array(0) {
    }
    ["order"]=> NULL
    ["resource"]=> NULL
    ["privilege"]=> NULL
    ["active"]=> bool(false)
    ["visible"]=> bool(true)
    ["type"]=> string(23) "Zym_Navigation_Page_Uri"
    ["pages"]=> array(0) {
    }
    ["uri"]=> string(1) "#"
  }
  [1]=> array(15) {
    ["label"]=> string(6) "Page 2"
    ["id"]=> NULL
    ["class"]=> NULL
    ["title"]=> NULL
    ["target"]=> NULL
    ["rel"]=> array(0) {
    }
    ["rev"]=> array(0) {
    }
    ["order"]=> NULL
    ["resource"]=> NULL
    ["privilege"]=> NULL
    ["active"]=> bool(false)
    ["visible"]=> bool(true)
    ["type"]=> string(23) "Zym_Navigation_Page_Uri"
    ["pages"]=> array(2) {
      [0]=> array(15) {
        ["label"]=> string(8) "Page 2.1"
        ["id"]=> NULL
        ["class"]=> NULL
        ["title"]=> NULL
        ["target"]=> NULL
        ["rel"]=> array(0) {
        }
        ["rev"]=> array(0) {
        }
        ["order"]=> NULL
        ["resource"]=> NULL
        ["privilege"]=> NULL
        ["active"]=> bool(false)
        ["visible"]=> bool(true)
        ["type"]=> string(23) "Zym_Navigation_Page_Uri"
        ["pages"]=> array(0) {
        }
        ["uri"]=> string(1) "#"
      }
      [1]=>
      array(15) {
        ["label"]=> string(8) "Page 2.2"
        ["id"]=> NULL
        ["class"]=> NULL
        ["title"]=> NULL
        ["target"]=> NULL
        ["rel"]=> array(0) {
        }
        ["rev"]=> array(0) {
        }
        ["order"]=> NULL
        ["resource"]=> NULL
        ["privilege"]=> NULL
        ["active"]=> bool(false)
        ["visible"]=> bool(true)
        ["type"]=> string(23) "Zym_Navigation_Page_Uri"
        ["pages"]=> array(0) {
        }
        ["uri"]=> string(1) "#"
      }
    }
    ["uri"]=> string(1) "#"
  }
}
*/