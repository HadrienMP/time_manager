<?php$config = array(    'preferences' => array(        array(            'field' => 'hours',            'label' => 'Heures',            'rules' => 'less_than[24]|greater_than[0]'        ),        array(            'field' => 'minutes',            'label' => 'Minutes',            'rules' => 'less_than[60]|greater_than[0]'        )    )                     );