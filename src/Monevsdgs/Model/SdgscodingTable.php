<?php

namespace Monevsdgs\Model;

class SdgscodingTable extends \Application\Model\SimrendaTable {

    protected $table = 'SDGS_Coding'; // table name
    protected $primary_key = 'id_coding'; // primary key field
    protected $auto_inc = true; // set false if pk is not identity / auto increment
    protected $fields = array(// list of table field(s) with default value
        'id_coding' => '',
        'id_indikator_kota' => '',
        'id_kegiatan_renstra' => '',
        'isDeleted' => 0
    );

}
