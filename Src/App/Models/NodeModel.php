<?php 

/*
 *  This model handles the base functions which all the models will use them
 */

namespace App\Models;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Exceptions\NotFoundException;

class NodeModel {
    

    protected string $ctype_id;
    protected $id = null;

    protected bool $load_fc = true;
    protected bool $deep_load = false;
    
    protected ?int $limit = null;
    protected array $select_fields = [];
    protected array $where = [];
    protected array $bind_values = [];
    protected array $order_by = [];
    protected ?int $page_no = null;
    protected ?int $rows_per_page = null;
    protected bool $display_name_as_title = false;
    protected ?string $cache_name = null;
    protected int $cache_ttl = 60;

    private $coreModel;
    private $ctype_obj;
    private $fields;

    public function __construct(?string $ctype_id = null){

        if(empty($ctype_id))
            return;
        $this->coreModel = Application::getInstance()->coreModel;

        $this->ctype_id = $ctype_id;

        if(_strlen($this->ctype_id) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Content-Type name is empty");
        }

        $this->ctype_obj = (new Ctype)->load($this->ctype_id);

        $this->fields = $this->ctype_obj->getFields();
    }

    public static function new($ctypeId) : NodeModel
    {
        return new NodeModel($ctypeId);
    }

    public function id($value) {
        $this->id = $value;

        return $this;
    }

    public function useCache(string $name, $ttl) {
        $this->cache_name = $name;
        $this->cache_ttl = $ttl;

        return $this;
    }

    public function DisplayNameAsTitle($value) {
        $this->display_name_as_title = $value;
        return $this;
    }


    public function bindValue($name, $value) {
        $this->bind_values[$name] = $value;

        return $this;
    }


    public function fields(array $value) {
        $this->select_fields = $value;

        return $this;
    }

    public function limit(?int $value) {
        $this->limit = $value;

        return $this;
    }

    public function where(string $value) {
        if(!empty($value))
            $this->where[] = (object)["andor" => "AND", "cond" => $value];

        return $this;
    }

    public function orWhere(string $value) {
        $this->where[] = (object)   ["andor" => "OR", "cond" => $value];

        return $this;
    }
    
    public function pagination(int $page_no, int $rows_per_page) {
        $this->page_no = $page_no;
        $this->rows_per_page = $rows_per_page;

        return $this;
    }

    public function loadFc(bool $value) {
        $this->load_fc = $value;

        return $this;
    }

    public function deepLoad(bool $value) {
        $this->deep_load = $value;

        return $this;
    }

    public function OrderBy(string $value) {
        $this->order_by[] = $value;

        return $this;
    }

    private function prepareWhere() {
        $condition = "";

        $i = 0;
        foreach($this->where as $item) {

            if($i++ > 0) {
                $condition .= " {$item->andor} ";
            }

            $condition .= $item->cond;
        }
        
        return $condition;
    }

    // private function getSettings() {
        
    //     return array (
    //         "where" => $this->prepareWhere(),
    //         "select_fields" => $this->select_fields,
    //         "load_fc" => $this->load_fc,
    //         "deep_load" => $this->deep_load,
    //         "limit" => $this->limit,
    //         "order_by" => $this->order_by,
    //         "bind_values" => $this->bind_values
    //     );

    // }

    public function load() {
        return $this->build();
    }

    public function loadFirst(string $ifFailmessage = null) {
        return $this->loadFirstOrFail($ifFailmessage);
    }

    public function loadFirstOrFail(string $ifFailmessage = null) {
        $data = $this->build();

        if(sizeof($data) == 0)
            throw new \App\Exceptions\NotFoundException($ifFailmessage ?? "Data not found");
        else
            return $data[0];
    }

    public function loadFirstOrDefault() {
        $data = $this->build();

        if(sizeof($data) == 0)
            return null;
        else
            return $data[0];
    }

    private function hasPagination() {
        return !empty($this->page_no) && !empty($this->rows_per_page);
    }

    private function buildLimitQry() {
        if(empty($this->limit) || $this->hasPagination())
            return " ";
        else 
            return " TOP {$this->limit}";
    }

    private function buildOrderByQry() {

        $order_by_qry = null;
        if(!empty($this->order_by)){
            $order_by_qry = implode(", ", $this->order_by);
        } else {

            if($this->ctype_obj->is_field_collection){

                $hasSortField = false;
                foreach($this->fields as $field){
                    
                    if($field->name == "sort"){
                        $hasSortField = true;
                        break;
                    }
                }

                if($hasSortField) {
                    $order_by_qry .= "m.sort";
                } else {
                    $order_by_qry .= "m.parent_id, m.id";
                }
                
            } else if (!isset($id)){
                $order_by_qry .= "m.id";
            }
        }

        return " ORDER BY {$order_by_qry} ";
    }


    private function buildWhere() {

        $where_condition = "";
        
        if(_strlen($this->id) > 0){
            if($this->ctype_obj->is_field_collection)
                $where_condition .= " m.parent_id = :id ";
            else 
                $where_condition .= " m.id = :id ";
        }
        if(_strlen($this->id) > 0) {
            $this->bindValue(":id", $this->id);
        }


        if(sizeof($this->where) > 0) {
            if(_strlen($where_condition) > 0) {
                $where_condition .= " AND ";
            }
            $where_condition .= "(";
        }

        $i = 0;
        foreach($this->where as $item) {

            if($i++ > 0) {
                $where_condition .= " {$item->andor} ";
            }

            $where_condition .= $item->cond;
        }
        
        if(sizeof($this->where) > 0) {
            $where_condition .= ")";
        }


        if(_strlen($where_condition) > 0) {
            $where_condition = " WHERE $where_condition";
        }

        return $where_condition;
    }


    private function buildJoins() {

        $result = array("FROM {$this->ctype_id} m");

        foreach($this->fields as $field){
            if(isset($field->ignore) && $field->ignore == true){
                continue;
            }
            if($field->field_type_id == "relation" && $field->is_multi != true && $field->data_source_value_column_is_text != true){
                $alias = "{$field->data_source_table_name}_{$field->name}";
                $result[] = "LEFT JOIN {$field->data_source_table_name} {$alias} ON {$alias}.{$field->data_source_value_column} = m.{$field->name}";
                if($field->data_source_id == 40){ //Users
                    $result[] = "LEFT JOIN positions {$alias}_positions ON {$alias}_positions.id = {$alias}.position_id";
                }
            }
        }

        return " " . implode("\n", $result);
    }

    private function buildSelect() {

        $result = array("'$this->ctype_id' as sett_ctype_id");
        
        if(empty($this->select_fields)) {
            $result[] = "m.*";
        }

        
        foreach($this->fields as $field){
            
            if(isset($field->ignore) && $field->ignore == true){
                continue;
            }

            if(!empty($this->select_fields)) {
                $result[] = "m.$field->name";
            }
            
            if($field->field_type_id == "relation") {

                if($field->is_multi == true){

                    if($field->data_source_value_column_is_text){
                        $result[] = "STUFF((SELECT '\n' + sf.value_id FROM " . $field->ctype_id . "_" . $field->name . " sf WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $field->name  . "_display";
                    } else {
                        if($this->display_name_as_title) {
                            $result[] = "STUFF((SELECT '\n' + s.name FROM " . $field->ctype_id . "_" . $field->name . " sf left join $field->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $field->name  . "_display";
                        } else {
                            $result[] = "STUFF((SELECT '\n' + s.$field->data_source_display_column FROM " . $field->ctype_id . "_" . $field->name . " sf left join $field->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $field->name  . "_display";
                        }
                        
                    
                    }  

                } else {
                    if($field->data_source_value_column_is_text != true){
                        if($this->display_name_as_title) {
                            $result[] = $field->data_source_table_name . "_" . $field->name . ".name as " . $field->name . "_display";
                        } else {
                            $result[] = $field->data_source_table_name . "_" . $field->name . ".$field->data_source_display_column as " . $field->name . "_display";
                        }
                        
                        if($field->data_source_id == 40){ //Users
                            $result[] = "CASE WHEN " . $field->data_source_table_name . "_" . $field->name . ".profile_picture_name is null then '" . DEFAULT_PROFILE_PICTURE_ANONYMOUS . "' ELSE " . $field->data_source_table_name . "_" . $field->name . ".profile_picture_name END  as " . $field->name . "_profile_picture";
                            $result[] = $field->data_source_table_name . "_" . $field->name . "_positions.name as " . $field->name . "_position";
                        }
                    }
                }
            }
        }

        return " " . implode(", ", $result);

    }

    private function prepareFields() {

        if(!empty($this->select_fields)) {
            
            foreach($this->fields as $field){
                
                if(in_array($field->name, $this->select_fields) || ($field->name == "id" && $field->is_system_field == true)){
                    $field->ignore = false;
                } else {
                    $field->ignore = true;
                }
            }
        }

    }


    private function buildPagination() {

        if($this->hasPagination() && !empty($this->limit)) {
            throw new \App\Exceptions\CriticalException("Unable to use pagination with Top clause");
        }

        
        if($this->hasPagination()) {
            return " OFFSET ({$this->page_no} - 1) * {$this->rows_per_page} ROWS FETCH NEXT {$this->rows_per_page} ROWS ONLY";
        }

        return "";
    }


    private function build() {

        if(isset($this->cache_name)) {
            $dataFromCache = Application::getInstance()->cache->get("node_model.$this->cache_name");
        
            if(isset($dataFromCache)) {
                return $dataFromCache;
            }
        }

        $this->prepareFields();

        $query = "
            SELECT " . 
            $this->buildLimitQry() . 
            $this->buildSelect() . 
            $this->buildJoins() . //Including From
            $this->buildWhere() . 
            $this->buildOrderByQry() .
            $this->buildPagination();

        $this->coreModel->db->query($query);
        
        //bindValues for where conditions
        foreach($this->bind_values as $key => $value) {
            $this->coreModel->db->bind($key, $value);
        }

        $results_main = $this->coreModel->db->resultSet('\App\Core\Node');

        if($this->deep_load == true){
            $results_main = $this->deepLoadAction($results_main);
        }
        
        $results_main = $this->processFc($results_main);
        if(isset($this->cache_name)) {
            Application::getInstance()->cache->set("node_model.$this->cache_name", $results_main, $this->cache_ttl);
        }
        
        return $results_main;
    }


    private function deepLoadAction($results_main) {

        foreach($results_main as &$res){
            foreach($this->fields as $field){
                if(isset($field->ignore) && $field->ignore == true){
                    continue;
                }
                if($field->field_type_id == "relation" && $field->is_multi != true && $field->data_source_value_column_is_text != true){
                    
                    if(!isset( $res->{$field->name}) || _strlen($res->{$field->name}) == 0)
                        continue;

                    $qry = "select m.* ";
            
                    $refFields = $field->getFields();
                    foreach($refFields as $refField){
                        
                        
                        if($refField->field_type_id == "relation" && $refField->is_multi == true  && $refField->data_source_value_column_is_text != true){
                            if($this->display_name_as_title) {
                                 $qry .= ",STUFF((SELECT '\n' + s.name FROM " . $refField->ctype_id . "_" . $refField->name . " sf left join $refField->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $refField->name  . "_display ";
                            } else {
                                $qry .= ",STUFF((SELECT '\n' + s.$refField->data_source_display_column FROM " . $refField->ctype_id . "_" . $refField->name . " sf left join $refField->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $refField->name  . "_display ";
                            }
                        } else if($refField->field_type_id == "relation" && $refField->is_multi == true  && $refField->data_source_value_column_is_text == true){
                            $qry .= ",STUFF((SELECT '\n' + sf.value_id FROM " . $refField->ctype_id . "_" . $refField->name . " sf WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $refField->name  . "_display ";
                        } else if($refField->field_type_id == "relation" && $refField->is_multi != true && $refField->data_source_value_column_is_text != true){
                            if($this->display_name_as_title) {
                                $qry .= " ," . $refField->data_source_table_name . "_" . $refField->name . ".name as " . $refField->name . "_display ";
                            } else {
                                $qry .= " ," . $refField->data_source_table_name . "_" . $refField->name . ".$refField->data_source_display_column as " . $refField->name . "_display ";
                            }
                        }
                    }
                    
                    $qry .= " from $field->data_source_table_name m ";

                    foreach($refFields as $refField){
                        if($refField->field_type_id == "relation" && $refField->is_multi != true && $refField->data_source_value_column_is_text != true){
                            $qry .= " LEFT JOIN " . $refField->data_source_table_name . " " . $refField->data_source_table_name . "_" . $refField->name . " ON " . $refField->data_source_table_name . "_" . $refField->name . ".$refField->data_source_value_column = m.$refField->name ";
                        }
                    }

                    
                    $qry .= " Where m.id = :id ";
                    
                
                    $this->coreModel->db->query($qry);
                
                    $this->coreModel->db->bind(':id', $res->{$field->name});

                    $sub_array = $this->coreModel->db->resultSet();
                    
                    $res = (array($res));
                    
                    $res = (object)array_merge( ((array)$res[0]), array( "$field->name" . "_detail" => $sub_array ) );

                }
            }
        }


        return $results_main;
    }

    private function processFc($results_main = []) {

        if(!isset($results_main)){
            $results_main = [];
        }
        
        $id_fc = "";
        
        foreach($results_main as $res){
            if(_strlen($id_fc) > 0)
                $id_fc .= ",";
            $id_fc .= $res->id;
        }
        
        
        foreach($results_main as &$res){

            $res = $res;
            
            foreach($this->fields as $field){
                if(isset($field->ignore) && $field->ignore == true){
                    continue;
                }
                if($field->field_type_id == "relation" && $field->is_multi == true){ //ComboBox Multi

                    $query = "select value_id as value, " . ($field->data_source_value_column_is_text ? "value_id" : "x.$field->data_source_display_column  ") . " as name from " . $field->ctype_id . "_" . $field->name . " s ";
                    if($field->data_source_value_column_is_text != true) {
                        $query .= " LEFT JOIN $field->data_source_table_name x on s.value_id = x.$field->data_source_value_column ";
                    }

                    $query .= "where parent_id = :id ";
                    $this->coreModel->db->query($query);
                    $this->coreModel->db->bind(':id', $res->id);
                    
                    $res = (array($res));
                    
                    $sub_array = $this->coreModel->db->resultSet();
                    
                    $res = (object)array_merge( ((array)$res[0]), array( "$field->name" => $sub_array ) );
                    

                } else if($field->field_type_id == "media" && $field->is_multi == true) {
                    
                    $this->coreModel->db->query("select * from " . $field->ctype_id . "_" . $field->name . "
                    where parent_id = :id");
                    $this->coreModel->db->bind(':id', $res->id);
                    
                    
                    $res = (array($res));
                    
                    $sub_array = $this->coreModel->db->resultSet();
                    $res = (object) array_merge( (array)$res[0], array( "$field->name" => $sub_array ) );

                } else if ($field->field_type_id == "field_collection"){ //FieldCollection
                    if($this->load_fc == false){
                        continue;
                    }
                    $qry = "select fc.* ";
                    
                    $fcFields = $field->getFields();

                    $sort_by_order = false;
                    
                    foreach($fcFields as $fc){
                        
                        if($fc->name == "sort"){
                            $sort_by_order = true;
                        }

                        if($fc->field_type_id == "relation" && $fc->is_multi == true && $fc->data_source_value_column_is_text != true){
                            if($this->display_name_as_title) {
                                $qry .= ",STUFF((SELECT '\n' + s.name FROM " . $fc->ctype_id . "_" . $fc->name . " sf left join $fc->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = fc.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $fc->name  . "_display ";
                            } else {
                                $qry .= ",STUFF((SELECT '\n' + s.$fc->data_source_display_column FROM " . $fc->ctype_id . "_" . $fc->name . " sf left join $fc->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = fc.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $fc->name  . "_display ";
                            }
                        } else if($fc->field_type_id == "relation" && $fc->is_multi == true  && $fc->data_source_value_column_is_text == true){
                            if($this->display_name_as_title) {
                                $qry .= ",STUFF((SELECT '\n' + sf.value_id FROM " . $fc->ctype_id . "_" . $fc->name . " sf WHERE sf.parent_id = fc.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $fc->name  . "_display ";
                            } else {
                                $qry .= ",STUFF((SELECT '\n' + sf.value_id FROM " . $fc->ctype_id . "_" . $fc->name . " sf WHERE sf.parent_id = fc.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $fc->name  . "_display ";
                            }
                        } else if($fc->field_type_id == "relation" && $fc->is_multi != true && $fc->data_source_value_column_is_text != true){
                            if($this->display_name_as_title) {
                                $qry .= " ," . $fc->data_source_table_name . "_" . $fc->name . ".name as " . $fc->name . "_display ";
                            } else {
                                $qry .= " ," . $fc->data_source_table_name . "_" . $fc->name . ".$fc->data_source_display_column as " . $fc->name . "_display ";
                            }
                            
                            
                        }
                    }
                    
                    $qry .= " from " . "$field->data_source_table_name fc ";
                    
                    foreach($field->getFields() as $fc){
                        if($fc->field_type_id == "relation" && $fc->is_multi != true && $fc->data_source_value_column_is_text != true){
                            $qry .= " LEFT JOIN " . $fc->data_source_table_name . " " . $fc->data_source_table_name . "_" . $fc->name . " ON " . $fc->data_source_table_name . "_" . $fc->name . ".$fc->data_source_value_column = fc.$fc->name ";
                        }
                    }

                    $qry .= " where fc.parent_id = :id ";

                    if($sort_by_order == true){
                        $qry .= " order by fc.sort ";
                    }
                    
                    if($sort_by_order != true){
                        $qry .= " order by fc.parent_id ";
                    }

                    $this->coreModel->db->query($qry);
                    
                    $this->coreModel->db->bind(':id', $res->id);
                    
                    
                    $res = (array($res));
                    
                    $sub_array = $this->coreModel->db->resultSet();
                    
                    if($this->deep_load == true){
                        //Deep loading
                        foreach($sub_array as &$res_sub){
                            foreach($field->getFields() as $fc){
                                if($fc->field_type_id == "relation" && $fc->is_multi != true && $fc->data_source_value_column_is_text != true){
                                    
                                    if(!isset( $res_sub->{$fc->name}) || _strlen($res_sub->{$fc->name}) == 0)
                                        continue;
            
                                    //echo "$field->name: " . $res_sub->{$field->name} . "<br>";
            
                                    $qry = "select m.* ";
                            
                                    $refFields = $fc->getFields();
                                    foreach($refFields as $refField){
                                        
                                        
                                        if($refField->field_type_id == "relation" && $refField->is_multi == true  && $refField->data_source_value_column_is_text != true){
                                            if($this->display_name_as_title) {
                                                $qry .= ",STUFF((SELECT '\n' + s.name FROM " . $refField->ctype_id . "_" . $refField->name . " sf left join $refField->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $refField->name  . "_display ";
                                            } else {
                                                $qry .= ",STUFF((SELECT '\n' + s.$refField->data_source_display_column FROM " . $refField->ctype_id . "_" . $refField->name . " sf left join $refField->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $refField->name  . "_display ";
                                            }
                                        } else if($refField->field_type_id == "relation" && $refField->is_multi == true  && $refField->data_source_value_column_is_text == true){
                                            if($this->display_name_as_title) {
                                                $qry .= ",STUFF((SELECT '\n' + sf.value_id FROM " . $refField->ctype_id . "_" . $refField->name . " sf WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $refField->name  . "_display ";
                                            } else {
                                                $qry .= ",STUFF((SELECT '\n' + sf.value_id FROM " . $refField->ctype_id . "_" . $refField->name . " sf WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $refField->name  . "_display ";
                                            }
                                        } else if($refField->field_type_id == "relation" && $refField->is_multi != true && $refField->data_source_value_column_is_text != true){
                                            
                                            if($this->display_name_as_title) {
                                                $qry .= " ," . $refField->data_source_table_name . "_" . $refField->name . ".name as " . $refField->name . "_display ";
                                            } else {
                                                $qry .= " ," . $refField->data_source_table_name . "_" . $refField->name . ".$refField->data_source_display_column as " . $refField->name . "_display ";
                                            }
                                        }
                                    }
                                    
                                    $qry .= " from $fc->data_source_table_name m ";
            
                                    foreach($refFields as $refField){
                                        if($refField->field_type_id == "relation" && $refField->is_multi != true && $refField->data_source_value_column_is_text != true){
                                            $qry .= " LEFT JOIN " . $refField->data_source_table_name . " " . $refField->data_source_table_name . "_" . $refField->name . " ON " . $refField->data_source_table_name . "_" . $refField->name . ".$refField->data_source_value_column = m.$refField->name ";
                                        }
                                    }
            
                                    //if(isset($where_str) && _strlen($where_str) > 0){
                                        $qry .= " Where m.id = :id ";
                                    //}
                                    
                                    $this->coreModel->db->query($qry);
                                
                                    $this->coreModel->db->bind(':id', $res_sub->{$fc->name});
            
                                    $sub_sub_array = $this->coreModel->db->resultSet();
                                    
                                    $res_sub = (array($res_sub));
                                    
                                    $res_sub = (object)array_merge( ((array)$res_sub[0]), array( "$fc->name" . "_detail" => $sub_sub_array ) );
            
                                }
                            }
                        }
                    }


                    
                    foreach($sub_array as &$res2){

                        foreach($field->getFields() as $fc){
                            if($fc->field_type_id == "relation" && $fc->is_multi == true){

                                $query = "select value_id as value, " . ($fc->data_source_value_column_is_text ? "value_id" : "x.name") . " as name from " . $fc->ctype_id . "_" . $fc->name . " s ";
                                if($fc->data_source_value_column_is_text != true) {
                                    $query .= " LEFT JOIN $fc->data_source_table_name x on s.value_id = x.$fc->data_source_value_column ";
                                }
                                
                                $query .= "where parent_id = :id ";
                                
                                $this->coreModel->db->query($query);
                                $this->coreModel->db->bind(':id', $res2->id);
                                
                                $res2 = (array($res2));
                                
                                $fc_sub_array = $this->coreModel->db->resultSet();
                                
                                $res2 = (object)array_merge( ((array)$res2[0]), array( "$fc->name" => $fc_sub_array ) );
                            } else if($fc->field_type_id == "media" && $fc->is_multi == true) {
                                //echo "p id: $res->id<br>";
                                $this->coreModel->db->query("select * from " . $fc->ctype_id . "_" . $fc->name . "
                                where parent_id = :id");
                                $this->coreModel->db->bind(':id', $res2->id);
                                
                                $res2 = (array($res2));
                                
                                $fc_sub_array = $this->coreModel->db->resultSet();
                                
                                $res2 = (object)array_merge( ((array)$res2[0]), array( "$fc->name" => $fc_sub_array ) );
                            }            
                        }
                    }

                    $res = (object) array_merge( (array)$res[0], array( "$field->name" => $sub_array ) );
                    
                }
            }

            
        }

        return $results_main;

    }
}
