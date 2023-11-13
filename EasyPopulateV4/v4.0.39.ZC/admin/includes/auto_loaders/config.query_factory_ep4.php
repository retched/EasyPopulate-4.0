<?php
/* loads and instantiates observer class within the admin file structure
*/
$autoLoadConfig[13][] = array(
    'autoType' => 'class',
    'loadFile' => 'class.query_factory_ep4.php',
    'classPath' => DIR_WS_CLASSES,
);

$autoLoadConfig[15][] = array(
    'autoType'=>'classInstantiate',
    'className'=>'queryFactoryEP4',
    'objectName'=>'ep4_db',
  );

