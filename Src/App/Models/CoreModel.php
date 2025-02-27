<?php

namespace App\Models;

use App\Core\Application;
use App\Core\Common\CTypeFieldHelper;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeFieldText;
use App\Core\Gctypes\DbStructureGenerator;
use App\Core\Node;
use \App\Exceptions\IlegalUserActionException;
use PDOException;
use Predis\Configuration\Option\Exceptions;

class CoreModel extends BaseModel
{

    public $db;

    public function __construct()
    {
        $this->db = new \App\Core\DAL\MainDatabase;
    }

    public function isPublicField($id = null)
    {

        $select = $this->newSelect()
            ->cols([
                'count(*) as result',
            ])
            ->from('ctypes_fields as f')
            ->join(
                'LEFT',
                'surveys as s',
                's.ctype_id = f.parent_id'
            )
            ->where('f.id = :id')
            ->where('isnull(f.is_hidden,0) = 0')
            ->where('isnull(f.is_system_field,0) = 0')
            ->where('f.data_source_filter_by_field_name is not null')
            ->where('f.data_source_filter_by_field_name_in_db is not null')
            ->bindValue('id', $id);

        $results = $this->db->querySelectSingle($select);

        if ($results->result > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function save_home_fav_widgets($list)
    {

        $query = "
            declare @user_id bigint = :user_id
            delete from home_user_widgets where user_id = @user_id\n";

        $i = 0;
        foreach ($list as $item) {
            $query .= "insert into home_user_widgets (user_id, widget_id, size, sort) values (@user_id, '$item->id', $item->size, " . $i++ . ")\n";
        }
        $this->db->query($query);
        $this->db->bind(':user_id', \App\Core\Application::getInstance()->user->getId());
        $this->db->execute();
    }

    public function get_widgets_by_permission($favorites_only = true)
    {
        
        $user_id = \App\Core\Application::getInstance()->user->getId();
        
        $dataFromCache = Application::getInstance()->cache->get("get_widgets_by_permission.$user_id.$favorites_only");
        if(isset($dataFromCache)) {
            return $dataFromCache;
        }

        $query = "
            declare @user_id bigint = :user_id
                select 
                    w.id,
                    w.name,
                    w.type,
                    w.colors,
                    w.description,
                    isnull(prop.size,12) as size,
                    w.tags
                from widgets w
                outer apply ( select count(*) as c from menu_items_roles pr where pr.parent_id = w.id ) roles
                left join dashboards d on d.id = w.dashboard_id
                outer apply (
                    select top 1 size, sort 
                    from home_user_widgets uw
                    where uw.widget_id = w.id and uw.user_id = @user_id
                ) prop
                ";

        if (Application::getInstance()->user->isAdmin() != true) {
            $query .= "
                
                outer apply (
                    select count(*) as r
                from users_roles r 
                where
                    r.parent_id = @user_id and
                    r.value_id in (select value_id from menu_items_roles ir left join menu_items ii on ii.id = ir.parent_id)
                ) x
                ";
        }

        $query .= "
                where
                    isnull(w.allow_to_show_on_homepage,0) = 1 " .
            (Application::getInstance()->user->isAdmin() != true ? " and x.r > 0 " : "") . " " .
            ($favorites_only ? "and w.name in (select w.name from home_user_widgets uw left join widgets w on w.id = uw.widget_id where uw.user_id = @user_id)" : "") . "
                order by isnull(prop.sort, 99999)
                ";


        $this->db->query($query);
        $this->db->bind(':user_id', $user_id);
        $results = $this->db->resultSet();

        Application::getInstance()->cache->set("get_widgets_by_permission.$user_id.$favorites_only", $results, 600);

        return $results;
    }


    public function get_module_items($module_id = null)
    {
        $this->db->query("
            declare @user_id bigint = :user_id   
            select  
                itm.description,
                itm.group_name,
                itm.id, itm.parent_id, itm.sort, itm.icon,
                case when itm.ctype_id is null then itm.url else '/' + c.name end as url,
                " . (isset($lang) ? " case when isnull(itm.name_" . $lang . ",'') = '' then itm.name else itm.name_" . $lang . " end " : "itm.name") . "  as name
            from modules_items itm
            outer apply ( select count(*) as c from modules_items_roles pr where pr.parent_id = itm.id ) roles
            left join modules m  on itm.parent_id = m.id 
            left join ctypes c on c.id = itm.ctype_id
            cross apply (
            select MAX(CAST(p.allow_read AS tinyint)) as has_permission 
            from ctypes_permissions p
            left join ctypes_permissions_roles r on r.parent_id = p.id
            where
                p.parent_id = c.id and
                (
                    r.value_id in (select value_id from users_roles where parent_id = @user_id)
                    or r.value_id in (select value_id from users_roles where parent_id in (select created_user_id from oic o where o.user_id = @user_id and date_from <= getdate() and getdate() <= date_to and isnull(is_disabled,0) != 1))
                ) and 
                c.id = c.id
            ) x 
            cross apply(
            select MAX(CAST(r.is_admin as tinyint)) as is_admin from users_roles ur left join roles r on r.id = ur.value_id where (ur.parent_id = @user_id or ur.parent_id in (select created_user_id from oic o where o.user_id = @user_id and date_from <= getdate() and getdate() <= date_to and isnull(is_disabled,0) != 1))
            ) a
                         
            cross apply (
                            select 
                                count(*) result 
                            from users_roles r 
                            where
                                (r.parent_id = @user_id or r.parent_id in (select created_user_id from oic o where o.user_id = @user_id and date_from <= getdate() and getdate() <= date_to and isnull(is_disabled,0) != 1)) and
                                r.value_id in (select value_id from modules_items_roles rp where rp.parent_id = itm.id)
                            ) g
            
            
            where 
                itm.parent_id = :parent_id and 
                (isnull(x.has_permission,0) = 1 or  isnull(a.is_admin,0) = 1 or (itm.ctype_id is null and (itm.url = '#' or isnull(itm.url,'') = '' ) 
                and roles.c = 0)
                or (roles.c > 0 and itm.ctype_id is null and g.result > 0) ) and isnull(itm.is_disabled,0) = 0
            
            order by itm.group_name, itm.sort
            
            ");
        $this->db->bind(':parent_id', $module_id);
        $this->db->bind(':user_id', \App\Core\Application::getInstance()->user->getId());
        $results = $this->db->resultSet();

        return $results;
    }




    public function getFields($ctype_id = null, $field_id = null, $field_name = null, $is_add_mode = true, $use_cache = true)
    {
        $data = $this->getFields_helper($ctype_id, $field_id, $field_name, $is_add_mode, $use_cache);

        $data = array_map(function ($object) {
            return clone $object;
        }, $data);

        return $data;
    }

    public function getFields_helper($ctype_id = null, $field_id = null, $field_name = null, $is_add_mode = true, $use_cache = true)
    {

        $user_id = Application::getInstance()->user->getid();
        $dataFromCache = Application::getInstance()->cache->get("get_fields.$ctype_id.$field_id.$field_name.$is_add_mode.$user_id");
        if(isset($dataFromCache)) {
            return $dataFromCache;
        }
        
        $lang_postfix = \App\Core\Application::getInstance()->user->getLangId();
        if(!empty($lang_postfix))
            $lang_postfix = "_" . $lang_postfix;
            
        $where = "";
        if (isset($ctype_id) || isset($field_id) || isset($field_name)) {

            if (isset($ctype_id) && _strlen($ctype_id) > 0)
                $where .= " f.parent_id = :parent_id ";

            if (isset($field_id) && _strlen($field_id) > 0) {
                if (_strlen($where) > 0)
                    $where .= " AND ";

                $where .= " f.id = :field_id ";
            }
            if (isset($field_name) && _strlen($field_name) > 0) {
                if (_strlen($where) > 0)
                    $where .= " AND ";

                $where .= " f.name = :field_name ";
            }

            if (_strlen($where) > 0)
                $where = " WHERE " . $where;
        }

        $qry = "
            declare @user_id bigint = :user_id

            
            SELECT 

                case when len(y.default_value) > 0 then y.default_value else f.default_value end as default_value_updated,

                case when y.is_hidden = 1 or isnull(f.is_read_only,0) = 1 then 1 else f.is_hidden end as is_hidden_updated_add,
                case when y.is_read_only = 1 then 1 else f.is_read_only end as is_read_only_updated_add,

                case when z.is_hidden = 1 then 1 else 0 end as is_hidden_updated_read,
            
                case when h.is_hidden = 1 then 1 else f.is_hidden end as is_hidden_updated_edit,
                case when h.is_read_only = 1 then 1 else f.is_read_only end as is_read_only_updated_edit,
                
                isnull(f.use_parent_permissions,0) as user_parent_permissions,
                f.*, 
                case when fx.c = 0 then f.data_source_display_column else f.data_source_display_column + '$lang_postfix' end as data_source_display_column,
                vp.value as validation_pattern,

                isnull(src.data_type_id, f.data_type_id) as data_type_id,
                isnull(src.str_length, f.str_length) as str_length,
                
                isnull(fts.dependencies ,'') + case when isnull(fts.dependencies ,'') != '' and isnull(fgs.dependencies ,'') != '' then ' && ' else '' end + 
				isnull(fls.dependencies ,'') + case when (isnull(fts.dependencies ,'') != '' and isnull(fls.dependencies ,'') != '' ) and isnull(fgs.dependencies,'') != '' then ' && ' else '' end +
				isnull(fgs.dependencies ,'') + case when (isnull(fls.dependencies ,'') != '' or isnull(fgs.dependencies ,'') != '' ) and isnull(f.dependencies,'') != '' then ' && ' else '' end + 
				isnull(f.dependencies,'') as dependencies,

                
                fta.id as appearance_id,
                case when fgs.sort is null then fgs.sort else fgs.sort end as group_sort,
                case when fts.sort is null then fts.sort else fts.sort end as tab_sort,
                 " . (!empty(\App\Core\Application::getInstance()->user->getLangId()) ? " case when isnull(f.title_" . \App\Core\Application::getInstance()->user->getLangId() . ",'') = '' then f.title else f.title_" . \App\Core\Application::getInstance()->user->getLangId() . " end " : " f.title ") . " as title,
                 " . (!empty(\App\Core\Application::getInstance()->user->getLangId()) ? " case when isnull(f.description_" . \App\Core\Application::getInstance()->user->getLangId() . ",'') = '' then f.description else f.description_" . \App\Core\Application::getInstance()->user->getLangId() . " end " : " f.description ") . " as description,
                 " . (!empty(\App\Core\Application::getInstance()->user->getLangId()) ? " case when isnull(f.validation_message_" . \App\Core\Application::getInstance()->user->getLangId() . ",'') = '' then f.validation_message else f.validation_message_" . \App\Core\Application::getInstance()->user->getLangId() . " end " : " f.validation_message ") . " as validation_message,
                t.id as ctype_id,
                t.name as ctype_name,
                pk_type.data_type_id as ctype_primary_column_type,
                pk_type.str_length as ctype_primary_column_length,
                src.id as data_source_table_name,
                dt.id as data_type_name,
                fty.extension as file_type_extension,
                isnull(t.is_field_collection,0) as is_field_collection,
                fcp.name as fc_parent_id,
                case when isnull(t.is_field_collection,0) = 0 then null else SUBSTRING(t.name, len(fcp.name) + 2, len(t.name)) end as fc_name

            FROM ctypes_fields f 
            outer apply (
				select count(*) c from ctypes_fields fxx where fxx.parent_id = f.data_source_id and fxx.name = f.data_source_display_column + '$lang_postfix'
			) fx
            left join validation_patterns vp on vp.id = f.validation_pattern_id
            left join field_type_appearances fta on fta.id = f.appearance_id

            outer apply (select top 1 g.sort, g.dependencies from ctypes_field_groups g where g.parent_id = f.parent_id and g.name = f.tab_name and g.type = 'tab') fts
			outer apply (select top 1 g.sort, g.dependencies  from ctypes_field_groups g where g.parent_id = f.parent_id and g.name = f.tab_name + ' - ' + f.location and g.type = 'location') fls
			outer apply (select top 1 g.sort, g.dependencies  from ctypes_field_groups g where g.parent_id = f.parent_id and g.name = f.tab_name + ' - ' + f.location + ' - ' + f.group_name and g.type = 'group') fgs
            
            LEFT JOIN ctypes t on t.id = f.parent_id
            outer apply (
                select 
                    src.*,
                    srcf.field_type_id,
                    srcf.data_type_id,
                    srcf.name as field_name,
                    srcf.str_length 
                from ctypes src 
                left join ctypes_fields srcf on srcf.parent_id = src.id and srcf.name = 'id'
                where src.id = f.data_source_id and f.field_type_id in ('relation','field_collection')
            ) src
            LEFT JOIN ctypes fcp on fcp.id = t.parent_ctype_id
            LEFT JOIN field_types dt on dt.id = f.field_type_id
            left join file_extension_types fty on fty.id = f.file_type_id
            left join (
            
                select
                    ff.value_id, 
                    p.parent_id, 
                    ISNULL(MIN(CAST(ISNULL(p.is_read_only,0) AS INT)),0) AS is_read_only,
                    ISNULL(MIN(CAST(ISNULL(p.is_hidden,0) AS INT)),0) AS is_hidden,
                    ISNULL(MIN(ISNULL(p.default_value,'')),'') AS default_value 
                from ctypes_field_permissions p
                cross apply (
                    select count(*) result from ctypes_field_permissions_roles r 
                    where r.parent_id = p.id and  
                        
                       (
                        r.value_id in (select value_id from users_roles where parent_id = @user_id)
                        or r.value_id in (select value_id from users_roles where parent_id in (select created_user_id from oic o where o.user_id = @user_id and date_from <= getdate() and getdate() <= date_to and isnull(is_disabled,0) != 1))
                       )
                        
                    ) x
                left join ctypes_field_permissions_field_names ff on ff.parent_id = p.id
                where
                    (
                        (isnull(p.inverse_roles,0) = 0 and x.result > 0)
                            or
                        (isnull(p.inverse_roles,0) = 1 and x.result = 0)
                    ) and 
                    isnull(p.is_add_mode,0) = 1
                group by 
                    ff.value_id, p.parent_id
            
            ) y on y.parent_id = f.parent_id  and y.value_id = f.name
            left join (
            
                select
                    ff.value_id, 
                    p.parent_id, 
                    ISNULL(MIN(CAST(ISNULL(p.is_read_only,0) AS INT)),0) AS is_read_only,
                    ISNULL(MIN(CAST(ISNULL(p.is_hidden,0) AS INT)),0) AS is_hidden,
                    ISNULL(MIN(ISNULL(p.default_value,0)),'') AS default_value 
                from ctypes_field_permissions p
                cross apply (
                    select count(*) result from ctypes_field_permissions_roles r 
                    where r.parent_id = p.id and  
                        
                        (
                        r.value_id in (select value_id from users_roles where parent_id = @user_id)
                        or r.value_id in (select value_id from users_roles where parent_id in (select created_user_id from oic o where o.user_id = @user_id and date_from <= getdate() and getdate() <= date_to and isnull(is_disabled,0) != 1))
                       )
                        
                    ) x
                left join ctypes_field_permissions_field_names ff on ff.parent_id = p.id
                where
                    (
                        (isnull(p.inverse_roles,0) = 0 and x.result > 0)
                            or
                        (isnull(p.inverse_roles,0) = 1 and x.result = 0)
                    ) and 
                    isnull(p.is_edit_mode,0) = 1
                group by 
                    ff.value_id, p.parent_id
            
            ) h on h.parent_id = f.parent_id  and h.value_id = f.name
            left join (
                select
                    ff.value_id, 
                    p.parent_id, 
                    ISNULL(MIN(CAST(ISNULL(p.is_read_only,0) AS INT)),0) AS is_read_only,
                    ISNULL(MIN(CAST(ISNULL(p.is_hidden,0) AS INT)),0) AS is_hidden,
                    ISNULL(MIN(ISNULL(p.default_value,0)),'') AS default_value 
                from ctypes_field_permissions p
                cross apply (
                    select count(*) result from ctypes_field_permissions_roles r 
                    where r.parent_id = p.id and  
                                
                       (
                        r.value_id in (select value_id from users_roles where parent_id = @user_id)
                        or r.value_id in (select value_id from users_roles where parent_id in (select created_user_id from oic o where o.user_id = @user_id and date_from <= getdate() and getdate() <= date_to and isnull(is_disabled,0) != 1))
                       )
                                
                    ) x
                left join ctypes_field_permissions_field_names ff on ff.parent_id = p.id
                where
                    (
                        (isnull(p.inverse_roles,0) = 0 and x.result > 0)
                            or
                        (isnull(p.inverse_roles,0) = 1 and x.result = 0)
                    ) and 
                    isnull(p.is_read_mode,0) = 1
                group by 
                    ff.value_id, p.parent_id
                    
            ) z on z.parent_id = f.parent_id  and z.value_id = f.name

            left join ctypes_field_groups tabindex on isnull(tabindex.name,'') = isnull(f.tab_name,'') and type = 'tab' and tabindex.parent_id = f.parent_id

            outer apply (
				select
                    data_type_id,
                    str_length
				from ctypes_fields where name = 'id' and parent_id = f.parent_id
			) pk_type


                $where 
                
                ORDER BY isnull(tabindex.sort,999), f.tab_name, fts.sort, isnull(location,'') desc, fgs.sort, isnull(f.group_name,''),f.sort";
        $this->db->query($qry);

        $this->db->bind(':user_id', Application::getInstance()->user->getId());
        if (isset($ctype_id) && _strlen($ctype_id))
            $this->db->bind(':parent_id', $ctype_id);

        if (isset($field_id) && _strlen($field_id))
            $this->db->bind(':field_id', $field_id);

        if (isset($field_name) && _strlen($field_name))
            $this->db->bind(':field_name', $field_name);


        $results = $this->db->resultSet('App\Core\Gctypes\CtypeField');
        
        $results = $this->mapFieldsClass($results);
        
        foreach($results as $field) {

            if(empty($field->required_condition) && $field->is_required)
                $field->required_condition = $field->dependencies;

        }

        
        Application::getInstance()->cache->set("get_fields.$ctype_id.$field_id.$field_name.$is_add_mode.$user_id", $results, 600);
        
        return $results;
    }

    private function mapFieldsClass($fields)
    {

        for ($i = 0; $i < sizeof($fields); $i++) {

            if ($fields[$i]->field_type_id == "text") {
                $fields[$i] = CtypeFieldText::fromArray((array)$fields[$i]);
            }
        }

        return $fields;
    }

    public function getdependencies($ctype_id = null, $dep_id = null)
    {

        $where = "";
        if (isset($ctype_id) || isset($field_id)) {
            $where = "";
            if (isset($ctype_id) && _strlen($ctype_id) > 0)
                $where .= " f.parent_id = :parent_id ";

            if (isset($dep_id) && _strlen($dep_id) > 0) {
                if (_strlen($where) > 0)
                    $where .= " AND ";

                $where .= " f.id = :dep_id ";
            }

            $where = " WHERE " . $where;
        }

        $this->db->query("
                SELECT 
                    f.*, 
                    t.name as ctype_name,
                    t.parent_id as ctype_id,
                    f1.id as f1_id,
                    f1.name as f1_name,
                    f1.id as f1_id,
                    f1.field_type_id as f1_field_type_id
                FROM ctypes_field_dependencies f 
                LEFT JOIN ctypes t on t.id = f.parent_id
                LEFT JOIN ctypes_fields f1 on f1.name = f.field_name and f1.parent_id = f.parent_id
                LEFT JOIN field_types dt1 on dt1.id = f1.field_type_id
                $where 
                ORDER BY f.sort");

        if (isset($ctype_id) && _strlen($ctype_id))
            $this->db->bind(':parent_id', $ctype_id);

        if (isset($dep_id) && _strlen($dep_id))
            $this->db->bind(':dep_id', $dep_id);

        $results = $this->db->resultSet();

        return $results;
    }





    public function getFileOrginalName($ctype_id, $field_name, $file_name, $is_multi = false)
    {

        if ($is_multi == true) {
            $this->db->query("select original_name from " . $ctype_id . "_" . $field_name . " where name = :file_name");
            $this->db->bind(':file_name', $file_name);
            $result = $this->db->resultSingle();
            if (isset($result)) {
                return $result->original_name;
            } else {
                return null;
            }
        } else {

            $this->db->query("select " . $field_name . "_original_name from " . $ctype_id . " where " . $field_name . "_name = :file_name");
            $this->db->bind(':file_name', $file_name);
            $result = $this->db->resultSingle();
            if (isset($result)) {
                return $result->{$field_name . "_original_name"};
            } else {
                return null;
            }
        }
    }

    public function getCtypes($ctype_id = null)
    {

        $data = $this->getCtypes_helper($ctype_id);

        if (isset($data)) {
            return clone ($data);
        } else {
            return null;
        }
    }

    public function getCtypes_helper($ctype_id = null)
    {

        $dataFromCache = Application::getInstance()->cache->get("get_ctypes.$ctype_id");
        if(isset($dataFromCache)) {
            return $dataFromCache;
        }
        
        $where = "";
        if (isset($ctype_id)) {

            if (isset($ctype_id) && _strlen($ctype_id) > 0)
                $where .= " t.id = :id ";

            $where = " WHERE " . $where;
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("ctype is empty");
        }

        $query = "SELECT TOP (1) 
            t.*, 
            " . (!empty(\App\Core\Application::getInstance()->user->getLangId()) ? " case when isnull(t.name_" . \App\Core\Application::getInstance()->user->getLangId() . ",'') = '' then t.name else t.name_" . \App\Core\Application::getInstance()->user->getLangId() . " end " : " t.name ") . " as name,
            case when isnull(t.is_field_collection,0) = 1 then t.parent_ctype_id  else t.id end as parent_ctype_id,
            m.icon as module_icon, m.name as module_name, m.code as module_code,
            pk_type.data_type_id as primary_column_type,
            isnull(nullif(t.display_field_name,''),'id') as display_field_name 
        from ctypes t 
        left join modules m on m.id = t.module_id 
        outer apply ( 
            select top 1 parent_id, c2.name as parent_name 
            from ctypes_fields f2 
            left join ctypes c2 on c2.id = f2.parent_id where f2.data_source_id = t.id
        ) as p 
        outer apply (
            select
                data_type_id
            from ctypes_fields where name = 'id' and parent_id = t.id
        ) pk_type

        $where 
        ORDER BY t.name";

        $this->db->query($query);
        if (isset($ctype_id)  && _strlen($ctype_id) > 0)
            $this->db->bind(':id', $ctype_id);
        
        $results = $this->db->resultSingle('\App\Core\Gctypes\Ctype');

        Application::getInstance()->cache->set("get_ctypes.$ctype_id", $results, 600);

        return $results;
    }






    public function save($data, $settings = array())
    {
        return \App\Models\Sub\SubNodeSave::legacy($data, $settings);
    }

    public function getCronStats($id = null, $limited = false) {
        $query = "
            SET NOCOUNT ON;

            Declare @CronId varchar(50) = :id
            Declare @limited bit = :limited
            Declare @FromDate Date
            if(isnull(@limited,0) = 0)
                SELECT @FromDate = isnull(created_date, getdate()) from crons where (@CronId is null or id = @CronId)
            else
                set @FromDate = dateadd(month,-1,getDate())
                
            set @FromDate = DATEFROMPARTS(year(@FromDate), month(@FromDate),1)
            declare @ToDate Date = EOMonth(getdate())

            ;With
            E1(N) As (Select 1 From (Values (1), (1), (1), (1), (1), (1), (1), (1), (1), (1)) DT(N)),
            E2(N) As (Select 1 From E1 A Cross Join E1 B),
            E4(N) As (Select 1 From E2 A Cross Join E2 B),
            E6(N) As (Select 1 From E4 A Cross Join E2 B),
            Tally(N) As
            (
            Select
            Row_Number() Over (Order By (Select Null))
            From
            E6
            )
            select
            DateAdd(Day, N - 1, @FromDate) date,
            year(DateAdd(Day, N - 1, @FromDate)) as year,
            right('00' + cast(month(DateAdd(Day, N - 1, @FromDate)) as varchar(2)), 2) as month,
            left(datename(m,DateAdd(Day, N - 1, @FromDate)),3) as month_name,
            day(DateAdd(Day, N - 1, @FromDate)) as day,
            started.c as started,
            failed.c as failed,
            data_synced.c as data_synced
            from Tally
            cross apply (
            select
                count(*) as c
            from crons_logs l
            where
            convert(date,l.created_date,103) = DateAdd(Day, N - 1, @FromDate) and (@CronId is null or l.cron_id = @CronId) and l.type_id = 'started'
            ) started
            cross apply (
            select
                count(*) as c
            from crons_logs l
            where
            convert(date,l.created_date,103) = DateAdd(Day, N - 1, @FromDate) and (@CronId is null or l.cron_id = @CronId) and l.type_id = 'failed'
            ) failed
            cross apply (
            select
                count(*) as c
            from crons_logs l
            where
            convert(date,l.created_date,103) = DateAdd(Day, N - 1, @FromDate) and (@CronId is null or l.cron_id = @CronId) and l.type_id = 'data_synced'
            ) data_synced
            where N <= DateDiff(Day, @FromDate, @ToDate) + 1
            order by tally.N


        ";

        $this->db->query($query);
        $this->db->bind(':id', $id);
        $this->db->bind(':limited', $limited);
        
        return $this->db->resultSet();
    }

    public function getCronsTasks($type_id = null, $cron_job_id = null, $load_full_detail = false, $status_id = null)
    {
        
        $query = "
                select 
                c.odk_form_main_table_name,
                c.id, 
                c.name,
                gr.name as group_name,
                c.is_custom,
                c.job_id,
                job.name as job_name,
                c.db_connection_string_id,
                c.is_system_object,
                c.version,
                c.type_id,
                ty.name as type_name,
                xy.message,
                xs.c as started_count,
                xf.c as failed_count,
                xd.c as data_synced_count,
                c.batch_size,
                c.created_date,
                x.created_date as last_run,
                x.type_id as last_run_status_id,
                x.type_name as last_run_status_name,
                x.duration,
                dbc.name as server,
                null as last_run_humanify,
                null as odk_created_date_humanify,
                null as created_date_humanify
            from crons c
            left join db_connection_strings dbc on dbc.id = c.db_connection_string_id
            left join crons_groups gr on gr.id = c.group_id
            left join crons_types ty on ty.id = c.type_id
            left join crons_jobs job on job.id = c.job_id
            outer apply (
            select 
                top 1
                l.type_id, l.message,l.created_date
            from crons_logs l 
            where 
                l.cron_id = c.id and
                convert(date,l.created_date,103) = convert(date,getdate(),103)
            order by l.created_date desc
            ) xy
            outer apply (
                select 
                    count(*) as c
                from crons_logs l 
                where 
                    l.cron_id = c.id and
                    l.type_id = 'started' and
                    l.created_date > dateadd(day,-1,getdate())
            ) xs
            outer apply (
                select 
                    count(*) as c
                from crons_logs l 
                where 
                    l.cron_id = c.id and
                    l.type_id = 'failed' and
                    l.created_date > dateadd(day,-1,getdate())
            ) xf
            outer apply (
                select 
                    count(*) as c
                from crons_logs l 
                where 
                    l.cron_id = c.id and
                    l.type_id = 'data_synced' and
                    l.created_date > dateadd(day,-1,getdate())
            ) xd
            outer apply (
                select top 1 
                    l.*, tt.name as type_name, datediff(MILLISECOND, s.created_date, e.created_date) as duration 
                from crons_logs l 
                left join crons_log_types tt on tt.id = l.type_id 
                outer apply (
                    select created_date from crons_logs where ukey = l.ukey and type_id = 'started'
                ) s
                outer apply (
                    select created_date from crons_logs where ukey = l.ukey and (type_id = 'finished' or type_id = 'failed') 
                ) e
                where l.cron_id = c.id and l.type_id in ('started','finished','failed') order by created_date desc
            ) x
            where c.status_id = 82 
                " . (empty($type_id) ? "" : " and c.type_id = :type_id") . "
                " . (empty($cron_job_id) ? "" : " and c.job_id = :cron_job_id") . "
                " . (empty($status_id) ? "" : " and x.type_id = :status_id") . "
                order by isnull(gr.name,''), c.name
            ";

        $this->db->query($query);
        
        if($type_id != null)
            $this->db->bind("type_id", $type_id);
        
        if($cron_job_id != null)
            $this->db->bind("cron_job_id", $cron_job_id);

        if($status_id != null)
            $this->db->bind("status_id", $status_id);

        $results = $this->db->resultSet();

        foreach ($results as $cron) {

            if($cron->last_run != null)
                $cron->last_run_humanify = \App\Helpers\DateHelper::humanify(strtotime($cron->last_run));
            
            if($cron->created_date != null)
                $cron->created_date_humanify = \App\Helpers\DateHelper::humanify(strtotime($cron->created_date));
            
            $cron->loadingRun = false;

            if($load_full_detail != true || $cron->type_id != 'sync_odk_form')
                continue;

            $pendingRecordsInfo = \App\Core\Crons\ODK::getStatistics($cron);

            $cron->all_records = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->all_records : 0);
            $cron->pending_records = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->pending_records : 0);
            $cron->incomplete_records = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->incomplete_records : 0);
            $cron->size = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->size_kb / 1024 : 0);

            $cron->error_message = isset($pendingRecordsInfo->error_message) ? $pendingRecordsInfo->error_message : null;
            
            $cron->last_submission_date = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->last_submission_date : null);
            $cron->last_submission_date_humanify = (isset($cron->last_submission_date) ? \App\Helpers\DateHelper::humanify(strtotime($cron->last_submission_date)) : null);
            $cron->last_submission_date_diff_day = (isset($cron->last_submission_date) ? abs(strtotime("now") - strtotime($cron->last_submission_date)) / (60 * 60 * 24) : null);
            $cron->odk_created_date = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->created_date : null);
            $cron->odk_created_date_humanify = (isset($cron->odk_created_date) ? \App\Helpers\DateHelper::humanify(strtotime($cron->odk_created_date)) : null);

            $cron->submission_allowed = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->submission_allowed : null);
            $cron->download_allowed = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->download_allowed : null);

            $cron->created_user = (isset($pendingRecordsInfo) ? \App\Core\Crons\BaseSyncOdkForm::cronDecodeUserNameFromString($pendingRecordsInfo->created_user) : null);

            
        }

        if (isset($results))
            return $results;
        else
            return "";
    }


    public function run_prepost_sp($ctype_obj, $sp_name, $id, $is_update = false)
    {

        $query = "EXEC $sp_name :id,:is_update";

        $this->db->query($query);

        $this->db->bind(':id', $id);
        $this->db->bind(':is_update', ($is_update == true ? 1 : 0));

        $results = $this->db->execute();
    }

    public function update_user_ref_columns_to_system_user()
    {

        $query = "
            SET NOCOUNT ON;
            SELECT   
                distinct
            OBJECT_NAME(f.parent_object_id) AS table_name,
            COL_NAME(fc.parent_object_id, fc.parent_column_id) AS column_name
            FROM sys.foreign_keys AS f  
            INNER JOIN sys.foreign_key_columns AS fc   
            ON f.object_id = fc.constraint_object_id   
            WHERE 
                f.referenced_object_id = OBJECT_ID('users') and 
                COL_NAME(fc.referenced_object_id, fc.referenced_column_id) = 'id' and
                delete_referential_action_desc = 'NO_ACTION' and 
                COL_NAME(fc.parent_object_id, fc.parent_column_id) in ('updated_user_id','created_user_id')";

        $this->db->query($query);
        $results = $this->db->resultSet();


        foreach ($results as $item) {

            $update = $this->newUpdate()
                ->table($item->table_name)
                ->set($item->column_name, ":value")
                ->bindValue('value', Application::getInstance()->user->getSystemUserId());
            $this->db->queryUpdate($update);
        }
    }

    public function get_stuck_tables()
    {
        $table_names = [];

        $ctypes = $this->nodeModel("ctypes")
            ->fields(["id", "name"])
            ->loadFc(false)
            ->where("isnull(m.is_field_collection,0) = 0")
            ->load();

        foreach ($ctypes as $ctype) {

            $table_names[] = $ctype->id;

            $fields = $this->getFields($ctype->id);


            foreach ($fields as $field) {
                if ($field->field_type_id == "field_collection") {

                    $table_names[] = $ctype->id . "_" . $field->name;

                    $fc_fields = $field->getFields();
                    foreach ($fc_fields as $fc) {

                        if ($fc->is_multi == true) {

                            $table_names[] = $ctype->id . "_" . $field->name . "_" . $fc->name;

                        }
                    }
                } else if ($field->is_multi == true) {

                    $table_names[] = $ctype->id . "_" . $field->name;

                }
            }
        }

        $query = "SET NOCOUNT ON;
            declare @temp table (name nvarchar(250))\n";

        foreach ($table_names as $tbl) {
            $query .= "insert into @temp (name) values ('" . $tbl . "')\n";
        }

        $query .= "
                SELECT t.TABLE_NAME as id, t.TABLE_NAME as name
                FROM INFORMATION_SCHEMA.TABLES t 
                LEFT JOIN @temp x on x.name = t.TABLE_NAME 
                WHERE x.name is null and t.TABLE_TYPE = 'BASE TABLE'
                ORDER BY x.name";

        $this->db->query($query);
        $results = $this->db->resultSet();

        return $results;
    }

    public function delete_stuck_tables($tables)
    {

        foreach ($tables as $tbl) {
            $query = "
                
                declare @sql nvarchar(max) = (
                    SELECT 
                        'alter table ' + sch.name + '.' + tab1.name + ' drop constraint ' + obj.name + ';'
                    FROM sys.foreign_key_columns fkc
                    INNER JOIN sys.objects obj
                        ON obj.object_id = fkc.constraint_object_id
                    INNER JOIN sys.tables tab1
                        ON tab1.object_id = fkc.parent_object_id
                    INNER JOIN sys.schemas sch
                        ON tab1.schema_id = sch.schema_id
                    INNER JOIN sys.columns col1
                        ON col1.column_id = parent_column_id AND col1.object_id = tab1.object_id
                    INNER JOIN sys.tables tab2
                        ON tab2.object_id = fkc.referenced_object_id
                    INNER JOIN sys.columns col2
                        ON col2.column_id = referenced_column_id AND col2.object_id = tab2.object_id
                        where
                            tab1.name ='" . $tbl . "' or tab2.name = '" . $tbl . "'
                    for xml path('')
                        
                );
                    
                
                IF (EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'dbo' AND  TABLE_NAME = '$tbl')) 
                BEGIN 

                    exec sp_executesql @sql;
                    
                    DROP TABLE $tbl
                END
                ";

            $this->db->query($query);

            $results = $this->db->execute();
        }
    }


    public function reset_table_numbering($ctype_id = null)
    {

        if (empty($ctype_id)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Content-Type is required");
        }

        $ctypeObj = $this->getCtypes($ctype_id);
        $query = "";

    }

    public function reset_table($ctype_id = null)
    {

        if (empty($ctype_id)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Content-Type is required");
        }

        $ctypeObj = $this->getCtypes($ctype_id);

        $delete = $this->newDelete();
        $delete->from($ctypeObj->id);
        $this->db->queryDelete($delete);

        $this->reset_table_numbering($ctype_id);
    }



    public function loadList($ctype_id, $fields, $pageNo, $rowsPerPage, &$pagination = "")
    {

        $qry = "select count(*) as result from $ctype_id ";

        $this->db->query($qry);
        $noOfRecordsInDb = $this->db->resultSingle()->result;

        $pagination = "<h1>" . $noOfRecordsInDb . "</h1>";
        $totalNoOfPages = ceil($noOfRecordsInDb / $rowsPerPage);

        if ($totalNoOfPages == 0)
            $totalNoOfPages = 1;
        if ($pageNo > $totalNoOfPages)
            $pageNo = $totalNoOfPages;

        $pagination = "<nav aria-label=\"Page navigation example\">
            <ul class=\"pagination\">";


        $pagination .= "    <li class=\"page-item " . (($pageNo <= 1) ? "disabled" : "") . "\"><a class=\"page-link\" href=\"/" . $ctype_id . "/?page=1\">First</a></li>";
        $pagination .= "    <li class=\"page-item " . (($pageNo <= 1) ? "disabled" : "") . "\"><a class=\"page-link\" href=\"/" . $ctype_id . "/?page=" . ($pageNo - 1) . "\">prev</a></li>";

        for ($j = 1; $j <= $totalNoOfPages; $j++) {
            if (abs($j - $pageNo) > 3)
                continue;

            if ($j == $pageNo)
                $pagination .= "    <li class=\"page-item active\"><a class=\"page-link\" href=\"/" . $ctype_id . "/?page=$j\">" . $this->getKeyword("Page") . " $j " . $this->getKeyword("of") . " $totalNoOfPages</a></li>";
            else
                $pagination .= "    <li class=\"page-item\"><a class=\"page-link\" href=\"/" . $ctype_id . "/?page=$j\">$j</a></li>";
        }
        $pagination .= "    <li class=\"page-item " . (($pageNo >= $totalNoOfPages) ? "disabled" : "") . "\"><a class=\"page-link\" href=\"/" . $ctype_id . "/?page=" . ($pageNo + 1) . "\">Next</a></li>";
        $pagination .= "    <li class=\"page-item " . (($pageNo >= $totalNoOfPages) ? "disabled" : "") . "\"><a class=\"page-link\" href=\"/" . $ctype_id . "/?page=" . $totalNoOfPages . "\">Last</a></li>";
        $pagination .= "</ul></nav>";


        $qry = "SELECT * FROM $ctype_id 
                ORDER BY id
                OFFSET " . $rowsPerPage * ($pageNo - 1) . " ROWS FETCH NEXT $rowsPerPage ROWS ONLY;";

        $this->db->query($qry);


        if (isset($id) && _strlen($id) > 0)
            $this->db->bind(':id', $id);

        $results = $this->db->resultSet();


        return $results;
    }



    public function getStatus($id = null, $ctype_id = null)
    {

        $lang = \App\Core\Application::getInstance()->user->getLangId();
        if(!empty($lang)) {
            $lang = "_" . $lang;
        }

        $where = "";
        if (!empty($id)) {
            $where .= "WHERE s.id = :id ";
        }

        $query = "
            SELECT 
                s.id, s.name$lang as name, s.style, 
                isnull(case when sett.id is null then isnull(s.is_justification_required,0) else sett.is_justification_required end,0) as is_justification_required, 
                isnull(case when sett.id is null then isnull(s.is_actual_date_required,0) else sett.is_actual_date_required end,0) as is_actual_date_required,
                s.actual_date_field_name,
                (SELECT r.value_id as id, u.name$lang as name
                    FROM ctypes_status_settings_allowed_reasons r 
                    left join ctypes_logs_reason_list u on u.id = r.value_id
                    LEFT join ctypes_status_settings st on st.id = r.parent_id
                    WHERE st.parent_id = sett.parent_id and st.status_id = s.id
                    FOR JSON PATH) as reasons_list
            FROM status_list s
            left join ctypes_status_settings sett on sett.status_id = s.id and sett.parent_id = " . (!empty($ctype_id) ? ":ctype_id" : "null") . "
            $where
            ";

            
        $this->db->query($query);

        if (!empty($id)) {
            $this->db->bind(':id', $id);
        }
        if (!empty($ctype_id)) {
            $this->db->bind(':ctype_id', $ctype_id);
        }

        $results = $this->db->resultSet();

        return $results;
    }

    public function getCtypesLog($ctype_id, $id, $user_id = null, $load_all_records = true)
    {
        $lang = \App\Core\Application::getInstance()->user->getLangId();
        if(!empty($lang)) {
            $lang = "_" . $lang;
        }
        
        $limit = 5;
        if ($load_all_records == null) {
            $load_all_records = true;
        }

        $query = "SELECT " . ($load_all_records == true ? " 0 as pending_log_records," : " TOP $limit COUNT(*) OVER() - $limit as pending_log_records,") . " parent_log_id, (SELECT * FROM ctypes_logs_attachments a WHERE a.parent_id = l.id FOR JSON PATH ) as attachments,'' as reply_justification, 0 as show_reply_box,c.name as ctype_name, u.gender_id, l.*, u.name as username, u.full_name as user_full_name, u.profile_picture_name, p.name as user_position, 
        (SELECT r.value_id as id, u.name$lang as name
            FROM ctypes_logs_reasons r 
            left join ctypes_logs_reason_list u on u.id = r.value_id
            WHERE r.parent_id = l.id
            FOR JSON PATH) as reasons_list
        FROM ctypes_logs l left join ctypes c on c.id = l.ctype_id left join users u on u.id = l.user_id left join positions p on p.id = u.position_id  
            WHERE 
                1 = 1 AND COALESCE(l.is_private, 0) = 0";
    
        if (isset($id))
            $query .= " AND l.content_id = :content_id ";

        if (isset($user_id))
            $query .= " AND l.user_id = :user_id ";

        if (isset($ctype_id))
            $query .= " AND c.id = :ctype_id ";

        $query .= " order by l.created_date desc";

        $this->db->query($query);

        if (isset($id))
            $this->db->bind(':content_id', $id);

        if (isset($ctype_id))
            $this->db->bind(':ctype_id', $ctype_id);


        if (isset($user_id))
            $this->db->bind(':user_id', $user_id);

        $results = $this->db->resultSet();
        
        foreach ($results as $res) {
            
            if (!isset($res->profile_picture_name) || _strlen($res->profile_picture_name) == 0) {
                if ($res->gender_id == 2) {
                    $res->profile_picture_name = DEFAULT_PROFILE_PICTURE_FEMALE;
                } else {
                    $res->profile_picture_name = DEFAULT_PROFILE_PICTURE_MALE;
                }
            }
            
            # nl2br does not accept null value
            if(isset($res->justification)){
                $res->justification = nl2br($res->justification);
            }
        }

        $ready_log = array();

        foreach ($results as $row) {
            $row->date_humanify =  \App\Helpers\DateHelper::humanify(strtotime($row->created_date));

            if (isset($row->parent_log_id) && _strlen($row->parent_log_id) > 0) {
            } else {

                $sub = $this->addChildLogs($results, $row);

                if (isset($sub) && $sub != array())
                    array_push($ready_log, $sub);
            }
        }

        return $ready_log;
    }

    private function addChildLogs($results, $parent_row)
    {

        $found = array();
        foreach ($results as $row) {

            if ($row->parent_log_id == $parent_row->id && isset($row->parent_log_id) && _strlen($row->parent_log_id) > 0) {

                $sub = $this->addChildLogs($results, $row);
                if ($sub != array()) {
                    array_push($found, $sub);
                }
            } else {
            }
        }

        if (isset($found) && $found != array()) {
            $parent_row->sub_items = $found;
        } else {
            $parent_row->sub_items = [];
        }

        return $parent_row;
    }

    

    public function getCronLog($id)
    {
        $query = "
            select 
                top 1000
                l.*, 
                tt.name as type_name, 
                u.full_name as user_full_name,
                u.profile_picture_name as user_profile_picture_name
            from crons_logs l 
            left join users u on u.id = l.user_id
            left join crons_log_types tt on tt.id = l.type_id 
            where l.cron_id = :id 
            order by id desc
            ";

        $this->db->query($query);


        $this->db->bind(':id', $id);

        $results = $this->db->resultSet();

        return $results;
    }


    public function updateCtypeStatus($ctypeId, $id, $status, $user_id, $justification, $reasons, $actual_date, $actual_date_field_name, $title = null)
    {

        $statusObj = $this->getStatus($status)[0];

        if ($actual_date == "null") {
            $actual_date = null;
        }

        $query = "UPDATE $ctypeId set token = newid(), status_id = :status " . (!empty($actual_date) && !empty($actual_date_field_name) ? "," . $actual_date_field_name . " = :$actual_date_field_name" : "") . " WHERE id = :id ";

        $this->db->query($query);

        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);

        if (!empty($actual_date) && !empty($actual_date_field_name)) {
            $this->db->bind(":$actual_date_field_name", $actual_date);
        }

        $results = $this->db->execute();

        (new CTypeLog($ctypeId))
            ->setContentId($id)
            ->setUserId($user_id)
            ->setJustification($justification)
            ->setTitle($statusObj->name)
            ->setGroupNam("change_status")
            ->setStatusId($status)
            ->setReasons($reasons)
            ->save();
        return $results;
    }

    public function GenerateDbSchemaForCtype($ctype_id)
    {
        $query = (new DbStructureGenerator($ctype_id))->generate();

        $this->db = new \App\Core\DAL\MainDatabase;
        $this->db->query($query);

        $this->db->execute();
    }



    public function getCtypesByLastUpdated($include_ext, $date = null)
    {

        $query = "exec core_changed_ctypes :include_ext, :date";

        $this->db->query($query);
        $this->db->bind(':date', $date);
        $this->db->bind(':include_ext', $include_ext);

        return $this->db->resultSet();
    }

    public function getFileTypes($id)
    {

        $query = "SELECT * FROM file_extension_types where id = :id";

        $this->db->query($query);


        $this->db->bind(':id', $id);

        $results = $this->db->resultSet();

        if (isset($results[0])) {
            return $results[0];
        } else {
            return null;
        }
    }

    public function markNotificationAsRead($id)
    {
        $query = "
                insert into notifications_users_seen (parent_id, value_id) VALUES (:id, :user_id)
                ";

        $this->db->query($query);


        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', Application::getInstance()->user->getId());

        $this->db->execute();
    }

    public function getNotificationsSummary($limit = 50, $only_unread = false)
    {
        return null;
        if (Application::getInstance()->user->isAuthenticated()) {

            //TODO: hardoced fid
            $query = "
                declare @user_id bigint = :user_id
                select 
                    " . (intval($limit) > 0 ? " TOP " . $limit : "") . "
                    count(*) as cnt,
                    n.title, 
                    n.message,
                    n.from_user_id, 
                    n.record_id, 
                    n.ctype_id, 
                    max(n.created_date) as created_date,
                    n.created_user_id, 
                    fu.name as user_name, 
                    fu.full_name as user_full_name, 
                    case when fu.profile_picture_name is null then case when fu.gender_id = 2 then '" . DEFAULT_PROFILE_PICTURE_FEMALE_FULL . "' else '" . DEFAULT_PROFILE_PICTURE_MALE_FULL . "' end else '/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=' + fu.profile_picture_name end as user_profile_picture,
                    case when s.cnt = 0 then 0 else 1 end as is_seen,
                    n.is_admin_notification,
                    type_id,
                    ty.name as type_id_display,
                    ty.background_color,
                    ty.text_color,
                    ty.icon
                from notifications n
                left join notification_types ty on ty.id = n.type_id
                cross apply (select count(*) as cnt from notifications_users_seen s where s.parent_id = n.id and s.value_id = @user_id) s
                left join users fu on fu.id = n.from_user_id
                left join notifications_to_users un on un.parent_id = n.id
                left join users u on u.id = un.value_id
                where	un.value_id = @user_id  " . ($only_unread == true ? " and s.cnt = 0" : "") . "
                group by n.title, 
                    n.message,
                    n.from_user_id, 
                    n.record_id, 
                    n.ctype_id, 
                    n.created_user_id, 
                    fu.name, 
                    fu.full_name, 
                    case when fu.profile_picture_name is null then case when fu.gender_id = 2 then '" . DEFAULT_PROFILE_PICTURE_FEMALE_FULL . "' else '" . DEFAULT_PROFILE_PICTURE_MALE_FULL . "' end else '/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=' + fu.profile_picture_name end ,
                    case when s.cnt = 0 then 0 else 1 end ,
                    n.is_admin_notification,
                    type_id,
                    ty.name,
                    ty.background_color,
                    ty.text_color,
                    ty.icon
                ORDER BY max(n.created_date) desc

                ";

            $this->db->query($query);

            $this->db->bind(':user_id', Application::getInstance()->user->getId());

            $results = $this->db->resultSet();

            return $results;
        } else {
            return null;
        }
    }

    public function getNotifications($limit = 50, $only_unread = false)
    {
        return null;
        if (Application::getInstance()->user->isAuthenticated()) {

            $query = "
                declare @user_id bigint = :user_id
                select 
                    " . (intval($limit) > 0 ? " TOP " . $limit : "") . "
                    n.id,
                    n.title, 
                    n.message,
                    n.from_user_id, 
                    n.record_id, 
                    n.ctype_id, 
                    n.created_date,
                    n.created_user_id, 
                    fu.name as user_name, 
                    fu.full_name as user_full_name, 
                    case when fu.profile_picture_name is null then case when fu.gender_id = 2 then '" . DEFAULT_PROFILE_PICTURE_FEMALE . "' else '" . DEFAULT_PROFILE_PICTURE_MALE . "' end else fu.profile_picture_name end as user_profile_picture,
                    case when s.cnt = 0 then 0 else 1 end as is_seen
                from notifications n
                cross apply (select count(*) as cnt from notifications_users_seen s where s.parent_id = n.id and s.value_id = @user_id) s
                left join users fu on fu.id = n.from_user_id
                left join notifications_to_users un on un.parent_id = n.id
                left join users u on u.id = un.value_id
                where	un.value_id = @user_id  " . ($only_unread == true ? " and s.cnt = 0" : "") . "
                ORDER BY n.created_date desc

                ";

            $this->db->query($query);


            $this->db->bind(':user_id', Application::getInstance()->user->getId());

            $results = $this->db->resultSet();

            return $results;
        } else {
            return null;
        }
    }

    public function getUnreadNotificationsCount()
    {
        return null;
        if (Application::getInstance()->user->isAuthenticated()) {

            $query = "
                declare @user_id bigint = :user_id
                select 
                    count(*) as cnt
                from notifications n
                cross apply (select count(*) as cnt from notifications_users_seen s where s.parent_id = n.id and s.value_id = @user_id) s
                cross apply (select count(*) as cnt from notifications_to_users s where s.parent_id = n.id and s.value_id = @user_id) t
                where
                    s.cnt = 0 and t.cnt > 0


                ";

            $this->db->query($query);


            $this->db->bind(':user_id', Application::getInstance()->user->getId());

            $results = $this->db->resultSingle();

            return $results->cnt;
        } else {
            return null;
        }
    }

    public function getRoleName($role_id)
    {

        $query = "
            select name from roles where id = :id
            ";

        $this->db->query($query);


        $this->db->bind(':id', $role_id);

        $results = $this->db->resultSingle();

        if (isset($results) && isset($results->{"name"})) {
            return $results->name;
        } else {
            return null;
        }
    }


    public function getCtypePermission(string $ctypeId, int $userId = null)
    {

        if (empty($userId)) {
            $userId = Application::getInstance()->user->getId();
        }

        if (empty($userId)) {
            return null;
        } else {

            $dataFromCache = Application::getInstance()->cache->get("getCtypePermission.$ctypeId.$userId");
            if(isset($dataFromCache)) {
                return $dataFromCache;
            }
            
            $query = "
                select 
                    CAST(ISNULL(MAX(CAST(ISNULL(allow_add,0) AS INT)),0) AS bit) AS allow_add, 
                    CAST(ISNULL(MIN(CAST(ISNULL(allow_edit_only_your_own_records,0) AS INT)),0) AS bit) AS allow_edit_only_your_own_records, 
                    CAST(ISNULL(MIN(CAST(ISNULL(allow_read_only_your_own_records,0) AS INT)),0) AS bit) AS allow_read_only_your_own_records, 
                    CAST(ISNULL(MIN(CAST(ISNULL(allow_delete_only_your_own_records,0) AS INT)),0) AS bit) AS allow_delete_only_your_own_records, 
                    CAST(ISNULL(MAX(CAST(ISNULL(allow_edit,0) AS INT)),0) AS bit) AS allow_edit, 
                    CAST(ISNULL(MAX(CAST(ISNULL(allow_delete,0) AS INT)),0) AS bit) AS allow_delete, 
                    CAST(ISNULL(MAX(CAST(ISNULL(allow_read,0) AS INT)),0) AS bit) AS allow_read,
                    CAST(ISNULL(MAX(CAST(ISNULL(allow_import_add,0) AS INT)),0) AS bit) AS allow_generic_import_add,
                    CAST(ISNULL(MAX(CAST(ISNULL(allow_import_edit,0) AS INT)),0) AS bit) AS allow_generic_import_edit,
                    CAST(ISNULL(MAX(CAST(ISNULL(allow_export,0) AS INT)),0) as bit) AS allow_generic_export,
                    CAST(ISNULL(MAX(CAST(ISNULL(allow_verify,0) AS INT)),0) AS bit) AS allow_verify,
                    CAST(ISNULL(MAX(CAST(ISNULL(allow_unverify,0) AS INT)),0) AS bit) AS allow_unverify,
                    CAST(ISNULL(MAX(CAST(ISNULL(allow_view_log,0) AS INT)),0) AS bit) AS allow_view_log
                from ctypes_permissions p
                cross apply (
                    select count(*) result from ctypes_permissions_roles r 
                    where r.parent_id = p.id and  r.value_id in (select value_id from users_roles where parent_id in (select id from core_FN_GetOicUsers(:user_id, DEFAULT)))) x
                where
                    parent_id = :ctype_id and x.result > 0
                ";

            $this->db->query($query);


            $this->db->bind(':ctype_id', $ctypeId);
            $this->db->bind(':user_id', $userId);

            $results = $this->db->resultSingle();

            Application::getInstance()->cache->set("getCtypePermission.$userId", $results, 600);

            return $results;
        }
    }


    public function getCurrentStatus($ctypeId, $contentId)
    {

        $lang = \App\Core\Application::getInstance()->user->getLangId();
        if(!empty($lang)) {
            $lang = "_" . $lang;
        }

        $query = "  
                SELECT
                    sc.id as current_status_id,
                    sc.name$lang as current_status_name,
                    sc.style as style
                FROM $ctypeId m
                LEFT JOIN status_list sc on sc.id = m.status_id
                WHERE m.id = :id
            ";

        $this->db->query($query);

        $this->db->bind(':id', $contentId);

        $result = $this->db->resultSingle();


        return $result;
    }

    public function getUserGovernorates($ctypeId = null, string $action = null, int $userId = null)
    {

        if (empty($userId)) {
            $userId = Application::getInstance()->session->get("user_id");
        }

        $rolesWhere = "";

        if (!empty($ctypeId) && !empty($action)) {
            $ctypeObj = $this->loadFirst("ctypes", $ctypeId);

            $roles = array();
            foreach ($ctypeObj->permissions as $itm) {
                if ($itm->{$action}) {
                    foreach ($itm->roles as $r) {
                        $roles[] = $r->value;
                    }
                }
            }


            if (!empty($roles)) {
                $val = "";
                foreach($roles as $role) {
                    if(_strlen($val) > 0)
                        $val .= ",";
                    $val .= "'$role'";
                }
                $rolesWhere = sprintf(' and r.value_id in (%s)', $val);
            }
        }

        if (empty($userId)) {
            return null;
        } else {

            $query = "
                SET NOCOUNT On;
                declare @user_id bigint = :id
                declare @temp table (id varchar(250))
                ";

            if (!empty($rolesWhere)) {

                $query .= "
                        
                    insert into @temp (id)
                    select
                        g.value_id
                    from users_detailed_permissions sub
                    left join users_detailed_permissions_roles r on r.parent_id = sub.id
                    left join users_detailed_permissions_governorates g on g.parent_id = sub.id
                    where 
                        sub.parent_id in (select id from core_FN_GetOicUsers(@user_id, DEFAULT)) $rolesWhere
                    
                        
                    ";
            }

            $query .= "

                if((select count(*) from @temp) = 0) 
                begin
                    insert into @temp (id)
                    select 
                        g.value_id
                    from users u 
                    left join users_governorates g on g.parent_id = u.id 
                    where u.id in (select id from core_FN_GetOicUsers(@user_id, DEFAULT))
                end

                
                insert into @temp (id)
                select 
                    g.value_id
                from users u 
                left join users_governorates g on g.parent_id = u.id 
                where u.id in (select id from core_FN_GetOicUsers(@user_id, 0))


                select STUFF((SELECT distinct ',' + cast(id as varchar(50)) from @temp where id IS NOT NULL FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as result
                
                ";

            $this->db->query($query);

            $this->db->bind(':id', $userId);

            $results = $this->db->resultSingle();

            if (isset($results->result))
                return $results->result;
            else
                return null;
        }
    }

    public function getUserUnits($ctypeId = null, string $action = null, int $userId = null)
    {

        if (empty($userId)) {
            $userId = Application::getInstance()->session->get("user_id");
        }

        $rolesWhere = "";

        if (!empty($ctypeId) && !empty($action)) {
            $ctypeObj = $this->loadFirst("ctypes", $ctypeId);

            $roles = array();
            foreach ($ctypeObj->permissions as $itm) {
                if ($itm->{$action}) {
                    foreach ($itm->roles as $r) {
                        $roles[] = $r->value;
                    }
                }
            }


            if (!empty($roles)) {
                $val = "";
                foreach($roles as $role) {
                    if(_strlen($val) > 0)
                        $val .= ",";
                    $val .= "'$role'";
                }
                $rolesWhere = sprintf(' and r.value_id in (%s)', $val);
            }
        }

        if (empty($userId)) {
            return null;
        } else {

            $query = "
                SET NOCOUNT On;
                declare @user_id bigint = :id
                declare @temp table (id varchar(250))
                ";

            if (!empty($rolesWhere)) {

                $query .= "
                    insert into @temp (id)
                    select
                        u.value_id
                    from users_detailed_permissions sub
                    left join users_detailed_permissions_roles r on r.parent_id = sub.id
                    left join users_detailed_permissions_units u on u.parent_id = sub.id
                    where 
                        sub.parent_id in (select id from core_FN_GetOicUsers(@user_id, DEFAULT)) $rolesWhere
                        
                    ";
            }

            $query .= "

                if((select count(*) from @temp) = 0) 
                begin
                    insert into @temp (id)
                    select 
                        uu.value_id
                    from users u 
                    left join users_units uu on uu.parent_id = u.id 
                    where u.id in (select id from core_FN_GetOicUsers(@user_id, DEFAULT))
                end

                insert into @temp (id)
                select 
                    uu.value_id
                from users u 
                left join users_units uu on uu.parent_id = u.id 
                where u.id in (select id from core_FN_GetOicUsers(@user_id, 0))
                
                select STUFF((SELECT distinct ',' + cast(id as varchar(50)) from @temp where id IS NOT NULL FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as result
                
                ";

            $this->db->query($query);

            $this->db->bind(':id', $userId);

            $results = $this->db->resultSingle();

            if (isset($results->result))
                return $results->result;
            else
                return null;
        }
    }

    // public function getUserProgrammes($ctypeId = null, string $action = null, int $userId = null){

    //     if(empty($userId)){
    //         $userId = Application::getInstance()->session->get("user_id");
    //     }

    //     $rolesWhere = "";

    //     if(!empty($ctypeId) && !empty($action)){
    //         $ctypeObj = $this->loadFirst("ctypes", $ctypeId);

    //         $roles = array();
    //         foreach($ctypeObj->permissions as $itm){
    //             if($itm->{$action}){
    //                 foreach($itm->roles as $r){
    //                     $roles[] = $r->value;
    //                 }
    //             }
    //         }


    //         if(!empty($roles)) {
    //             $rolesWhere = sprintf(' and r.value_id in (%s)', implode(",", $roles));
    //         }
    //     }

    //     if(empty($userId)){
    //         return null;
    //     } else {

    //         $query = "
    //         SET NOCOUNT On;
    //         declare @user_id bigint = :id
    //         declare @temp table (id varchar(250))
    //         declare @oicUserId bigint
    //         ";

    //         if(!empty($rolesWhere)) {

    //             $query .= "

    //             insert into @temp (id)
    //             select
    //                 p.value_id
    //             from users_detailed_permissions sub
    //             left join users_detailed_permissions_roles r on r.parent_id = sub.id
    //             left join users_detailed_permissions_programmes p on p.parent_id = sub.id
    //             where 
    //                 sub.parent_id in (select id from core_FN_GetOicUsers(@user_id, DEFAULT)) $rolesWhere


    //             ";
    //         }

    //         $query .= "

    //         if((select count(*) from @temp) = 0) 
    //         begin
    //             insert into @temp (id)
    //             select 
    //                 p.value_id
    //             from users u 
    //             left join users_programmes p on p.parent_id = u.id 
    //             where u.id in (select id from core_FN_GetOicUsers(@user_id, DEFAULT))
    //         end

    //         insert into @temp (id)
    //         select 
    //             p.value_id
    //         from users u 
    //         left join users_programmes p on p.parent_id = u.id 
    //         where u.id in (select id from core_FN_GetOicUsers(@user_id, 0))

    //         select STUFF((SELECT ',' + cast(id as varchar(50)) from @temp where id IS NOT NULL FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as result

    //         ";

    //         $this->db->query($query);

    //         $this->db->bind(':id', $userId);

    //         $results = $this->db->resultSingle();

    //         if(isset($results->result))
    //             return $results->result;
    //         else
    //             return null;

    //     } 

    // }



    public function getUserFormTypes($ctypeId = null, string $action = null, int $userId = null)
    {

        if (empty($userId)) {
            $userId = Application::getInstance()->session->get("user_id");
        }

        $rolesWhere = "";

        if (!empty($ctypeId) && !empty($action)) {
            $ctypeObj = $this->loadFirst("ctypes", $ctypeId);

            $roles = array();
            foreach ($ctypeObj->permissions as $itm) {
                if ($itm->{$action}) {
                    foreach ($itm->roles as $r) {
                        $roles[] = $r->value;
                    }
                }
            }


            if (!empty($roles)) {
                $val = "";
                foreach($roles as $role) {
                    if(_strlen($val) > 0)
                        $val .= ",";
                    $val .= "'$role'";
                }
                $rolesWhere = sprintf(' and r.value_id in (%s)', $val);
            }
        }

        if (empty($userId)) {
            return null;
        } else {

            $query = "
                SET NOCOUNT On;
                declare @user_id bigint = :id
                declare @temp table (id varchar(250))
                ";

            if (!empty($rolesWhere)) {

                $query .= "
                        
                    insert into @temp (id)
                    select
                        uu.parent_id
                    from users_detailed_permissions sub
                    left join users_detailed_permissions_roles r on r.parent_id = sub.id
                    left join users_detailed_permissions_units u on u.parent_id = sub.id
                    left join form_types_units uu on uu.value_id = u.value_id
                    where 
                        sub.parent_id in (select id from core_FN_GetOicUsers(@user_id, DEFAULT)) $rolesWhere
                        
                        
                    ";
            }

            $query .= "
                             
                if((select count(*) from @temp) = 0) 
                begin
                    insert into @temp (id)
                    select 
                        t.parent_id
                    from users u 
                    left join users_units uu on uu.parent_id = u.id 
                    left join form_types_units t on t.value_id = uu.value_id
                    where u.id in (select id from core_FN_GetOicUsers(@user_id, DEFAULT))
                end

                insert into @temp (id)
                select 
                    t.parent_id
                from users u 
                left join users_units uu on uu.parent_id = u.id 
                left join form_types_units t on t.value_id = uu.value_id
                where u.id in (select id from core_FN_GetOicUsers(@user_id, 0))

                select STUFF((SELECT distinct ',' + cast(id as varchar(50)) from @temp where id IS NOT NULL FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as result
                
                ";

            $this->db->query($query);

            $this->db->bind(':id', $userId);

            $results = $this->db->resultSingle();

            if (isset($results->result))
                return $results->result;
            else
                return null;
        }
    }



    public function getUsersBasedOnRole($role)
    {
        if (isset($role)) {

            $query = "select distinct u.id from users u left join users_roles r on u.id = r.parent_id where r.value_id in ($role)";

            $this->db->query($query);

            $results = $this->db->resultSet();

            if (isset($results))
                return $results;
            else
                return "";
        } else {
            return "";
        }
    }


    public function getFormType($id)
    {

        $query = "select * from form_types where id = :id";

        $this->db->query($query);

        $this->db->bind(':id', $id);

        $results = $this->db->resultSingle();

        if (isset($results))
            return $results;
        else
            return null;
    }

    public function getStatusWorkflowId($id, $form_type)
    {

        $query = "select top 1 * from status_workflow_templates where id = :id or id = :full_name order by id desc";

        $this->db->query($query);

        $this->db->bind(':id', $id);
        $this->db->bind(':full_name', $id . "_" . $form_type);

        $results = $this->db->resultSet();

        if ($results != array())
            return $results[0];
        else
            return null;
    }

    public function reset_sp_fn_in_db()
    {
        
    }

    public function delete_menu($id)
    {

        $query = "
            declare @MenuId varchar(50) = :id
            if((select isnull(is_system_object,0) as system_object from menu where id = @MenuId) = 1)
            begin
                delete from menu_items where parent_id = @MenuId and isnull(is_system_object,0) = 0
            end else begin
                delete from menu where id = @MenuId
            end

            ";

        $this->db->query($query);

        $this->db->bind(':id', $id);

        $results = $this->db->execute();
    }



    public function addCronLog($ukey, $cron_id, $type_id, $message, $reference_id = null, $record_id = null)
    {

        $query = "
                declare @ukey nvarchar(255) = :ukey
                declare @message nvarchar(255) = :message
                declare @type_id varchar(50) = :type_id
                declare @reference_id nvarchar(255) = :reference_id
                declare @record_id bigint = :record_id

                insert into crons_logs (ukey, cron_id, message, type_id, reference_id, user_id, record_id) values (@ukey, :cron_id, @message, @type_id, @reference_id, :user_id, @record_id)
                
                ";



        $this->db->query($query);

        $this->db->bind(':user_id', Application::getInstance()->user->getId());
        $this->db->bind(':cron_id', $cron_id);
        $this->db->bind(':type_id', $type_id);
        $this->db->bind(':message', $message);
        $this->db->bind(':ukey', $ukey);
        $this->db->bind(':reference_id', $reference_id);
        $this->db->bind(':record_id', $record_id);

        $results = $this->db->execute();

        return $results;
    }


    public function deleteFile($isMulti, $ctypeId, $id, $fieldName, $fileName)
    {

        if ($isMulti != 1) {
            $query = "UPDATE $ctypeId SET " . $fieldName . "_name = NULL, " . $fieldName . "_size = NULL, " . $fieldName . "_extension = NULL, " . $fieldName . "_type = NULL, " . $fieldName . "_original_name = NULL WHERE id = :id";

            $this->db->query($query);

            $this->db->bind(':id', $id);

            $this->db->execute();
        } else {
            $query = "DELETE FROM " . $ctypeId . "_$fieldName WHERE parent_id = :id AND name = :name";

            $this->db->query($query);

            $this->db->bind(':id', $id);
            $this->db->bind(':name', $fileName);

            $this->db->execute();
        }
    }

    public function getUserIdByEmail($email)
    {
        $this->db->query('SELECT id FROM users WHERE email = :email');
        //Bind Value
        $this->db->bind(':email', $email);

        $result = $this->db->resultSet();
        if (isset($result) && $result != array())
            return $result[0]->id;
        else
            return null;
    }

    public function getUserIdByName($name)
    {
        
        $this->db->query('SELECT id FROM users WHERE name = :name');
        //Bind Value
        $this->db->bind(':name', $name);

        $result = $this->db->resultSet();
        if (isset($result) && $result != array())
            return $result[0]->id;
        else
            return null;
    }


    public function checkAuriIfExist($ctype_id, $odk_auri)
    {

        $this->db->query("SELECT COUNT(*) as cnt FROM $ctype_id WHERE odk_auri = :odk_auri");
        //Bind Value
        $this->db->bind(':odk_auri', $odk_auri);

        $result = $this->db->resultSingle();
        if (isset($result) && $result->cnt == 0)
            return false;
        else
            return true;
    }

    public function flagEmailAsSent($id, $handlerClass)
    {

        $query = "UPDATE emails SET status_id = 94, sent_date = getdate(), handler_class = :handler_class WHERE id = :id";

        $this->db->query($query);

        $this->db->bind(':id', $id);
        $this->db->bind(':handler_class', $handlerClass);

        $this->db->execute();
    }

    public function flagEmailAsHasError($id)
    {

        $query = "UPDATE emails SET status_id = 73 WHERE id = :id";

        $this->db->query($query);

        $this->db->bind(':id', $id);

        $this->db->execute();
    }

    public function flagSMSAsSent($id, $handlerClass, $refId = null)
    {

        $query = "UPDATE sms SET status_id = 94, sent_date = getdate(), handler_class = :handler_class, ref_id = :ref_id WHERE id = :id";

        $this->db->query($query);

        $this->db->bind(':id', $id);
        $this->db->bind(':handler_class', $handlerClass);
        $this->db->bind(':ref_id', $refId);

        $this->db->execute();
    }

    public function flagSMSAsHasError($id)
    {

        $query = "UPDATE sms SET status_id = 73 WHERE id = :id";

        $this->db->query($query);

        $this->db->bind(':id', $id);

        $this->db->execute();
    }




    public function runChartDataSource($chart, $postData, $use_secondary_query = false)
    {

        return (new \App\Models\Sub\ChartDataSource($chart, $postData, $use_secondary_query))->main();
    }







    public function verifyRecord($ctypeId, $id, $value = 1)
    {

        if ($value == 1)
            $query = "UPDATE $ctypeId SET is_verified = 1 WHERE id = :id";
        else
            $query = "UPDATE $ctypeId SET is_verified = 0 WHERE id = :id";

        $this->db->query($query);

        $this->db->bind(':id', $id);

        $this->db->execute();

        if ($value == 1)
        {
            // $this->addCtypeLog($ctypeId, $id, null, null, "Verified");
            (new CTypeLog($ctypeId))
                ->setContentId($id)
                ->setTitle("Verified")
                ->setGroupNam("verification")
                ->save();
        } else {
            // $this->addCtypeLog($ctypeId, $id, null, null, "Un-Verified");
            (new CTypeLog($ctypeId))
                ->setContentId($id)
                ->setTitle("Un-Verified")
                ->setGroupNam("verification")
                ->save();
        }
    }





    public function get_other_currency_rate()
    {

        $query = "SELECT top 1 rate from currencies where isnull(is_default,0) = 0 order by id";

        $this->db->query($query);

        $result = $this->db->resultSingle();

        return $result->rate;
    }






    public function getMenu($id)
    {

        $lang = \App\Core\Application::getInstance()->user->getLangId();
        if (\App\Core\Application::getInstance()->user->isGuest())
            return null;
        
        $user_id = \App\Core\Application::getInstance()->user->getId();

        $dataFromCache = Application::getInstance()->cache->get("get_menu.$user_id.$id");
        if(isset($dataFromCache)) {
            return $dataFromCache;
        }

        $qry = "
            SET ANSI_WARNINGS OFF   
            declare @user_id bigint = :user_id
            select 
                itm.id, itm.parent_id, itm.parent_menu_id, itm.sort, itm.code,
                case when itm.ctype_id is null then itm.url else '/' + c.id end as url,
                case when itm.name is null or len(itm.name) = 0 then c.name else " . (isset($lang) ? " case when isnull(itm.name_" . $lang . ",'') = '' then itm.name else itm.name_" . $lang . " end " : "itm.name") . "  end as name
            from menu m 
            left join menu_items itm on itm.parent_id = m.id 
            outer apply ( select count(*) as c from menu_items_roles pr where pr.parent_id = itm.id ) roles
            left join ctypes c on c.id = itm.ctype_id
            cross apply (
            select MAX(CAST(isnull(nullif(p.allow_read,0), isnull(p.allow_read_only_your_own_records,0)) AS tinyint)) as has_permission 
            from ctypes_permissions p
            left join ctypes_permissions_roles r on r.parent_id = p.id
            where
                p.parent_id = c.id and
                (r.value_id in (select value_id from users_roles where parent_id in (select id from core_FN_GetOicUsers(@user_id, DEFAULT))   ) ) and c.id = c.id
            ) x 
            cross apply(
            select MAX(CAST(r.is_admin as tinyint)) as is_admin from users_roles ur left join roles r on r.id = ur.value_id where (ur.parent_id in (select id from core_FN_GetOicUsers(@user_id, DEFAULT))  )
            ) a
            
            cross apply (
                            select 
                                count(*) result 
                            from users_roles r 
                            where
                                (r.parent_id in (select id from core_FN_GetOicUsers(@user_id, DEFAULT))  ) and
                                r.value_id in (select value_id from menu_items_roles ir left join menu_items ii on ii.id = ir.parent_id where ii.id = itm.id)
                            ) g


            where m.id = :id and 
                (
                    isnull(x.has_permission,0) = 1 or  isnull(a.is_admin,0) = 1 or 
                    (itm.ctype_id is null and (itm.url = '#' or isnull(itm.url,'') = '' ) and roles.c = 0) or 
                    (roles.c > 0 and itm.ctype_id is null and g.result > 0) 
                ) and isnull(itm.is_disabled,0) = 0

            order by itm.sort
        ";

        $this->db->query($qry);

        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':id', $id);

        $results = $this->db->resultSet();

        $ready_menu = array();

        foreach ($results as $row) {

            if (isset($row->parent_menu_id) && _strlen($row->parent_menu_id) > 0) {
            } else {

                $sub = $this->addChildMenus($results, $row);

                if (isset($sub) && $sub != array())
                    array_push($ready_menu, $sub);
            }
        }

        $ready_menu = (object)$ready_menu;

        Application::getInstance()->cache->set("get_menu.$user_id.$id", $ready_menu, 600);

        return $ready_menu;
    }

    private function addChildMenus($results, $parent_row)
    {

        $found = array();
        foreach ($results as $row) {

            if ($row->parent_menu_id == $parent_row->id && isset($row->parent_menu_id) && _strlen($row->parent_menu_id) > 0) {

                $sub = $this->addChildMenus($results, $row);
                if ($sub != array()) {
                    array_push($found, $sub);
                }
            } else {
            }
        }

        if (isset($found) && $found != array())
            $parent_row->sub_menu = (object)$found;
        else {
            if (_strlen($parent_row->url) == 0 && !isset($parent_row->ctype_id))
                return null;
        }

        return $parent_row;
    }


















    // public function addTrackRequest($url, $params = null, $ctype_id = null, $content_id = null, $is_blocked = false, $error_404  = null, $is_helper = null)
    // {
    //     return;
    //     $this->db->query("insert into request_tracker   (is_helper, error_404,is_blocked,url,params, ctype_id, content_id, browser, ip_address, created_date,created_user_id,is_mobile,os_name) VALUES 
    //                                                     (:is_helper, :error_404, :is_blocked, :url,:params, :ctype_id, :content_id,:browser, :ip_address, getdate(), :created_user_id,:is_mobile,:os_name)");

    //     //Bind Value
    //     $this->db->bind(':url', $url);
    //     $this->db->bind(':params', $params);
    //     $this->db->bind(':is_blocked', $is_blocked);
    //     $this->db->bind(':ctype_id', $ctype_id);
    //     $this->db->bind(':content_id', $content_id);
    //     $this->db->bind(':error_404', $error_404);
    //     $this->db->bind(':is_helper', $is_helper);
    //     $this->db->bind(':os_name', get_os_name());

    //     $this->db->bind(':is_mobile', get_is_mobile());
    //     $this->db->bind(':browser', get_browser_name());
    //     $this->db->bind(':ip_address', Application::getInstance()->request->getClientIPAddress());
    //     $this->db->bind(':created_user_id', Application::getInstance()->user->getId());



    //     $this->db->execute();
    // }

    // Get User by ID
    public function RecordExistsOrTokenChanged($ctypeObj, $id, $token = null)
    {

        $this->db->query("select TOP 1 " . (!empty($token) ? "m.token," : "") . "m.created_date, m.last_update_date, m.created_user_id, m.updated_user_id, cu.full_name as created_user_full_name, uu.full_name as updated_user_full_name from $ctypeObj->id m left join users cu on cu.id = m.created_user_id left join users uu on uu.id = m.updated_user_id where m.id = :id");
        // Bind value
        $this->db->bind(':id', $id);

        $row = $this->db->resultSet();

        if ($row == array()) {
            return "Record id " . $id . " not found in $ctypeObj->name to update";
        }

        if (!empty($token) && _strtolower($row[0]->token) != _strtolower($token)) {
            $token_user_full_name = "Unknown";
            $token_user_id = null;

            if (!empty($row[0]->updated_user_full_name)) {
                $token_user_full_name = $row[0]->updated_user_full_name;
                $token_user_id = $row[0]->updated_user_id;
            } else if (!empty($row[0]->created_user_full_name)) {
                $token_user_full_name = $row[0]->created_user_full_name;
                $token_user_id = $row[0]->created_user_id;
            }

            if (isset($token_user_id) && Application::getInstance()->user->getId() !== null && $token_user_id = Application::getInstance()->user->getId()) {
                $token_user_full_name = "You";
            }

            $token_date = "Unknown";
            if (!empty($row[0]->last_update_date)) {
                $token_date = date_format(date_create($row[0]->last_update_date), "d/m/Y H:i:s");
            } else if (!empty($row[0]->created_date)) {
                $token_date = date_format(date_create($row[0]->created_date), "d/m/Y H:i:s");
            }
            return "Record id " . $id . " updated by $token_user_full_name " . (\App\Core\Application::getInstance()->user->isAdmin() == true ? "on $token_date" : "") . ", session of this page is expired. Please reload the page to get latest content and redo your changes.";
        }

        return null;
    }


    public function get_user_id_by_gov_unit_role($role_id, $governorate_id = null, $unit_id = null)
    {
        $query = "EXEC core_get_user_id_by_gov_unit_role :role_id, :governorate_id, :unit_id";

        $this->db->query($query);

        $this->db->bind(':role_id', $role_id);
        $this->db->bind(':governorate_id', $governorate_id);
        $this->db->bind(':unit_id', $unit_id);

        $result = $this->db->resultSet();

        return $result;
    }







    public function update_ip_address_info($ip_address, $continent_code = null, $continent_name = null, $country_code = null, $country_name = null, $city = null, $zip = null, $lat = null, $lng = null, $type = null)
    {

        $query = "UPDATE sec_ip_address SET info_is_updated=1,continent_code = :continent_code ,continent_name=:continent_name,country_code=:country_code,country_name=:country_name,city=:city,zip=:zip,lat=:lat,lng=:lng,type=:type,updated_user_id=:updated_user_id WHERE ip_address = :ip_address";

        $this->db->query($query);

        $this->db->bind(':ip_address', $ip_address);
        $this->db->bind(':continent_code', $continent_code);
        $this->db->bind(':continent_name', $continent_name);
        $this->db->bind(':country_name', $country_name);
        $this->db->bind(':country_code', $country_code);
        $this->db->bind(':city', $city);
        $this->db->bind(':zip', $zip);
        $this->db->bind(':lat', $lat);
        $this->db->bind(':lng', $lng);
        $this->db->bind(':type', $type);
        $this->db->bind(':updated_user_id', \App\Core\Application::getInstance()->user->getId());


        $this->db->execute();
    }

    public function ip_address_blocked_detail($ip_address)
    {

        $query = "
        select top 1u.full_name as user_name, u.id as user_id, i.created_date from sec_ip_black_list i
        left join users u on u.id = i.created_user_id
        where ip_address = :ip_address
        ";

        $this->db->query($query);

        $this->db->bind(':ip_address', $ip_address);

        $result = $this->db->resultSingle();

        return $result;
    }

    public function refresh_ip_address()
    {
        $query = "
        insert into sec_ip_address (ip_address)
        select distinct ip_address from request_tracker where ip_address not in ('127.0.0.1','::1') and ip_address not in (select ip_address from sec_ip_address)
        ";

        $this->db->query($query);

        $this->db->execute();
    }

    public function get_ip_address_requests($ip_address)
    {

        $query = "
        select
            top 1000
	        r.created_date, r.created_user_id as user_id, u.full_name as user_name, isnull(browser,'Unknown') as browser, ip_address, is_mobile, isnull(os_name,'Unknown') as os_name, error_404, is_blocked, is_helper, url, params
        from request_tracker r
        left join users u on u.id = r.created_user_id
        where r.ip_address = :ip_address
        order by created_date desc

        ";

        $this->db->query($query);

        $this->db->bind(':ip_address', $ip_address);

        $result = $this->db->resultSet();

        return $result;
    }

    public function query_execute($qry)
    {

        $this->db->query($qry);

        $result = $this->db->execute();

        return $result;
    }


    public function get_document_file_attachments($doc_name = null, $file_name = null, $return_first_one = true)
    {

        $where_str = "";
        if (!empty($doc_name)) {
            $where_str .= "doc.id = :doc_name";
        }

        if (!empty($file_name)) {
            if (!empty($where_str)) {
                $where_str .= " AND ";
            }

            $where_str .= "f.name = :file_name";
        }

        if (!empty($doc_name) != true && !empty($file_name) != true) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Doc name or file name should be passed");
        }

        $query = "
        select
            " . ($return_first_one == true ? " TOP 1 " : "") . "
            f.name as file_name, f.title as file_title, f.sort as file_sort, a.name, a.size, a.extension, a.original_name, a.type, '" . UPLOAD_DIR_FULL . DS."documents".DS . "' + a.name as full_path 
        from documents doc 
        left join documents_files f on f.parent_id = doc.id
        left join documents_files_attachments a on a.parent_id = f.id
        where
            $where_str
        order by f.sort
        ";

        $this->db->query($query);

        $this->db->bind(':doc_name', $doc_name);
        $this->db->bind(':file_name', $file_name);

        $result = $this->db->resultSet();

        if ($result == array()) {
            throw new \App\Exceptions\NotFoundException("Document not found");
        }

        if ($return_first_one) {
            return $result[0];
        }
        return $result;
    }



    public function getCtypeRecordCount($ctypeId)
    {

        $ctypeObj = $this->nodeModel("ctypes")
            ->fields(["id", "name"])
            ->where("m.id = :id")
            ->bindValue("id", $ctypeId)
            ->loadFirstOrFail();

        $select = $this->newSelect()
            ->cols([
                'SUM( P.rows ) as result',
            ])
            ->from('sys.tables As T')
            ->join(
                'LEFT',
                'sys.partitions as P',
                'P.OBJECT_ID = T.OBJECT_ID'
            )
            ->join(
                'LEFT',
                'sys.schemas as S',
                'T.schema_id = S.schema_id'
            )
            ->where('T.is_ms_shipped = 0')
            ->where('P.index_id IN (1,0)')
            ->where('T.type = \'U\'')
            ->where('S.name  = \'dbo\' and T.name = :ctype_name')
            ->groupBy(["S.name"], ["T.name"])
            ->bindValue('ctype_name', $ctypeObj->id);

        $results = $this->db->querySelectSingle($select);

        return $results->result;
    }



    public function getHelpCategories($keyword = null)
    {

        $searchWhere = "";
        if (_strlen($keyword) > 0) {
            foreach (_explode(" ", $keyword) as $tag) {
                if (_strlen($searchWhere) > 0) {
                    $searchWhere .= " OR ";
                }
                $searchWhere .= "tags like '%$tag%'";
            }
        }

        if (_strlen($searchWhere) > 0) {
            $searchWhere = " AND ($searchWhere) ";
        }

        $select = $this->newSelect()
            ->cols(['id', 'name', 'sort', 'color', 'sub_categories', 'description', 'no_of_posts'])
            ->fromSubSelect(
                'select 
                    c.id, c.name, c.description, c.color, isnull(c.sort, 9999) as sort, isnull(x.c,0) as no_of_posts,
                        sub_categories = \'[\' + STUFF((
                            SELECT \',{"name":"\' + sub.name + \'", "color": "\' + sub.color + \'"}\'
                            FROM help_sub_categories sub
                            WHERE sub.parent_category_id = c.id
                            FOR XML PATH(\'\'), TYPE).value(\'.\', \'NVARCHAR(MAX)\'), 1, 1, \'\') + \']\'
                    from help_categories c
                    outer apply (
                        select count(*) as c from help_posts where category_id = c.id ' . $searchWhere . '
                    ) x
                    outer apply (
                        select
                            count(*) as c
                        from help_categories_roles rl 
                        left join users_roles ur on ur.value_id = rl.value_id
                        where rl.parent_id = c.id and ur.parent_id = ' . Application::getInstance()->user->getId() . '
            
                    ) p
                    WHERE P.c > 0
                    ',
                'm'
            )
            ->where("no_of_posts > 0")
            ->orderBy(["sort"]);

        $results = $this->db->querySelect($select);

        return $results;
    }


    public function get_my_last_activities()
    {

        $query = "
        select
        top 500
            c.id as ctype_id, c.name as ctype_name, l.title, l.justification, l.date, l.content_id
        from ctypes_logs l 
        left join ctypes c on c.id = l.ctype_id
        where l.created_user_id = :user_id and isnull(l.created_date,103) <= convert(date,dateadd(month,-1, getdate()),103)
        order by l.created_date desc

        ";


        $this->db->query($query);

        $this->db->bind(':user_id', Application::getInstance()->user->getId());

        return $this->db->resultSet();
    }


    public function bg_tasks_get($status_id = null)
    {

        if (!in_array($status_id, [1, 3, 22, 28, 73])) {
            $status_id = null;
        }

        $query = "
        declare @statusId bigint = :status_id
        select 
            isnull(t.is_deleted,0) as is_deleted,
            t.id, 
            t.name, 
            t.action_name, 
            t.main_value, 
            t.post_data, 
            t.status_id, 
            t.created_date,
            t.completion_date,
            datediff(second, isnull(t.start_date, t.created_date),isnull(t.completion_date, getdate())) as elapsed_time_sec,
            s.name as status_name,
            t.start_date, 
            t.completion_date, 
            t.output_file_name, 
            t.output_file_original_name, 
            t.output_file_size, 
            t.output_file_type, 
            t.output_file_extension,
            t.last_error
        from bg_tasks t
        left join status_list s on s.id = t.status_id
        where 
            t.created_user_id = :user_id and 
            (@statusId is null or t.status_id = @statusId) and
            isnull(t.is_deleted,0) = 0 and t.status_id != 3
        order by t.created_date desc
        ";


        $this->db->query($query);

        $this->db->bind(':user_id', Application::getInstance()->user->getId());
        $this->db->bind(':status_id', $status_id);

        return $this->db->resultSet();
    }

    public function bg_tasks_cancel($id)
    {

        $query = "update bg_tasks set status_id = 3 where id = :id";

        $this->db->query($query);

        $this->db->bind(':id', $id);

        $this->db->execute();

        // Application::getInstance()->coreModel->addCtypeLog("bg_tasks", $id, null, null, "Task cancelled by user");
        (new CTypeLog("bg_tasks"))
            ->setContentId($id)
            ->setTitle("Task cancelled by user")
            ->setGroupNam("edit")
            ->save();
    }

    public function bg_tasks_delete($id)
    {

        $query = "update bg_tasks set is_deleted = 1 where id = :id";

        $this->db->query($query);

        $this->db->bind(':id', $id);

        $this->db->execute();

        // Application::getInstance()->coreModel->addCtypeLog("bg_tasks", $id, null, null, "Task deleted");
        (new CTypeLog("bg_tasks"))
            ->setContentId($id)
            ->setTitle("Task deleted")
            ->setGroupNam("edit")
            ->save();
    }

    public function fixStuckBgTasks()
    {
        $select = $this->newSelect()
            ->cols(["count(*) as c"])
            ->from("bg_tasks")
            ->where('status_id = 28')
            ->where('DATEDIFF(hour, start_date, getdate()) > 12');

        $result = $this->db->querySelectSingle($select);
        
        if($result->c > 0) {
            $update = $this->newUpdate()
                ->table("bg_tasks")
                ->set("output_file_name", null)
                ->set("output_file_original_name", null)
                ->set("output_file_extension", null)
                ->set("output_file_type", null)
                ->set("output_file_size", null)
                ->set("status_id", 73)
                ->set("completion_date", "getdate()")
                ->set("last_error", "'Timeout'")
                ->where('status_id = 28')
                ->where('DATEDIFF(hour, start_date, getdate()) > 12');

            $this->db->queryUpdate($update);

            return $result->c;
        }

        return 0;
        
    }

    function getAllOdkDatabases()
    {
        return $this->nodeModel("db_connection_strings")
            ->where("isnull(m.is_active,0) = 1")
            ->where("category = 'odk'")
            ->load();
    }


    
    public function getCtypeOrphanColumnsData($ctypeId) 
    {
        $result = [];

        $fields = CTypeFieldHelper::loadByCtypeId($ctypeId);

        $list = [];
        foreach($fields as $field) {
            if($field->getIsMulti() || in_array($field->getFieldTypeId(), ["button","note","component"])) {
                continue;
            }

            if($field->getFieldTypeId() == "media") {
                $list[] = $field->getName() . "_name";
                $list[] = $field->getName() . "_original_name";
                $list[] = $field->getName() . "_extension";
                $list[] = $field->getName() . "_type";
                $list[] = $field->getName() . "_size";
            } else {
                $list[] = $field->getName();
            }
            
        }

        $fieldsList = "";
        foreach($list as $item) {
            if(_strlen($fieldsList) > 0) $fieldsList .= ",";
            $fieldsList .= sprintf("'%s'", $item);
        }

        $dbName = Application::getInstance()->env->get("DB_NAME");
        
        $query = "
        select
            c.table_name,
            c.column_name,
            c.data_type,
            null as loading,
            case when c.IS_NULLABLE = 'YES' then null else 1 end as is_required,
            'ALTER TABLE [{$dbName}].dbo.[{$ctypeId}] DROP COLUMN ' + c.column_name as delete_script,
            'ALTER TABLE [{$dbName}].dbo.[{$ctypeId}] ALTER COLUMN ' + c.column_name + ' ' + c.data_type + ' NULL' as allow_null_script
        from INFORMATION_SCHEMA.COLUMNS c
        where 
            c.TABLE_CATALOG = '{$dbName}' and c.TABLE_SCHEMA = 'dbo' and c.TABLE_NAME = '{$ctypeId}'
            and c.COLUMN_NAME not in ($fieldsList)
        ";

        $this->db->query($query);

        return $this->db->resultSet();

    }

    public function deleteCtypeOrphanColumn($ctypeId, $columnName) 
    {
        
        $data = $this->getCtypeOrphanColumnsData($ctypeId);

        foreach($data as $item) {
            if($item->column_name == $columnName) {
                
                $this->db->query($item->delete_script);

                $this->db->execute();
            }
        }

    }


    public function getAttachmentFieldsByCtype(string $ctypeId)
    {
        $query = "
        select
            c.id as ctype_id,
            f.name as field_name,
            c.is_field_collection,
            isnull(p.id,c.id) as parent_ctype_id,
            f.is_multi
        from ctypes c
        left join ctypes_fields f on f.parent_id = c.id
        left join ctypes p on p.id = c.parent_ctype_id
        where f.field_type_id = 'media' and isnull(p.id,c.id) = :ctype_id

        ";

        $this->db->query($query);
        $this->db->bind(":ctype_id", $ctypeId);

        $fields = $this->db->resultSet();

        $query2 = "";
        
        if(sizeof($fields) == 0)
            return [];
            
        $i = 0;
        foreach($fields as $item) {
            
            if($i++ > 0)
                $query2 .= "\nUNION\n";

            if($item->is_multi){
                $query2 .= "select 	name from {$item->ctype_id}_{$item->field_name}\n"; //, original_name, size, type, extension
            } else {
                $query2 .= "select {$item->field_name}_name as name from $item->ctype_id\n"; //{$item->field_name}_original_name as original_name, {$item->field_name}_size as size, {$item->field_name}_type as type, {$item->field_name}_extension as extension
            }

        }

        $this->db->query($query2);

        $result = $this->db->resultSet("array");

        $result = array_map(function($arr) {
            return $arr['name'];
        }, $result);
        
        return $result;
    }



    public function cleanUpBgTasks()
    {
        
        $bgTasksCleanUpAfter = Application::getInstance()->settings->get("sys_bg_tasks_cleanup_after_days", 90);
        
        $select = $this->newSelect()
            ->cols([
                'count(*) as result',
            ])
            ->from('bg_tasks as bg')
            ->where('datediff(day, created_date, getdate()) > :days')
            ->bindValue('days', $bgTasksCleanUpAfter);

        $results = $this->db->querySelectSingle($select);

        if ($results->result > 0) {
            
            $this->db->query("DELETE FROM bg_tasks WHERE datediff(day, created_date, getdate()) > :days");
            $this->db->bind("days", $bgTasksCleanUpAfter);
            $this->db->execute();

        }

        return $results->result;
    }


    public function getErrorLogNotification() {
        $query = "
        with cte as (
            select top 1000
                title,
                count(*) as value
            from error_log e
            where e.created_date >= dateadd(day,-1,getdate())
            group by title
        )

        select 
            cte.title,
            cte.value as value_today,
            yes.value as value_yesterday
        from cte
        cross apply (
            select
                count(*) as value
            from error_log e
            where 
                cte.title = e.title and 
                e.created_date >= dateadd(DAY,-2,GETDATE()) and
                e.created_date <= dateadd(DAY,-1,GETDATE()) 
        ) yes
        order by cte.value desc

        ";

        $this->db->query($query);
        $result = $this->db->resultSet();
        return $result;
        
    }

    public function getCtypeLogsSummary() {
        $query = "
         with cte as (
            select
                c.name as ctype_name,
                title,
                count(*) as total,
                sum(count(*)) over (partition by c.name) as grant_total
            from ctypes_logs l
            left join ctypes c on c.id = l.ctype_id
            where l.created_date >= dateadd(day,-1,getdate())
            group by c.name, title
            )

            select
                *,
                count(*) over(partition by ctype_name) as items_count
            from cte
            order by cte.ctype_name
        ";

        $this->db->query($query);
        $result = $this->db->resultSet();
        return $result;
        
    }

    public function resetTableNumbering(string $ctypeId){
        $query = "
            BEGIN TRANSACTION;

                DECLARE @newID INT;            
                DECLARE @ctypeId NVARCHAR(255) = :ctypeId;
            
                -- Check if id field is numeric
                IF (select field_type_id from ctypes_fields where name = 'id' and parent_id = @ctypeId) != 'number'
                BEGIN
                    THROW 50000, 'Resetting Non-Numeric IDs Is Not Allowed.', 1;
                END
                ELSE
                BEGIN
                    -- Check if the table has any rows
                    SET @newID = ISNULL((SELECT TOP 1 id FROM ". $ctypeId ." WITH (TABLOCKX) ORDER BY id desc),0)

                    -- Reset the auto-increment
                    DBCC CHECKIDENT (@ctypeId, RESEED, @newID); 
            
                    DELETE FROM ctypes_logs 
                    WHERE ctype_id = @ctypeId
                    AND (
                        @newID IS NULL OR
                        content_id > @newID
                    );
                END

            COMMIT;
        ";

        $this->db->query($query);

        $this->db->bind(':ctypeId', $ctypeId);

        try {
            $this->db->execute();
        } catch (PDOException $exc) {
            
            throw new IlegalUserActionException($exc->getMessage());
        }

        (new CTypeLog("ctypes"))
                    ->setContentId($ctypeId)
                    ->setUserId(\App\Core\Application::getInstance()->user->getId())
                    ->setTitle("Ctype Logs Were Cleared After Resetting Auto Increment IDs")
                    ->setGroupNam("reset")
                    ->save();
    }
}
