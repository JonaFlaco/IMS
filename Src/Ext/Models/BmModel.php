<?php

namespace Ext\Models;

use App\Core\Application;
use App\Core\DAL\MainDatabase;
use App\Models\CoreModel;
use App\Models\CTypeLog;
use Exception;
use IlegalUserActionException;

class BmModel extends ExtModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getBeneficiaryDuplicates($id, $fullName, $nationalIdNo, $passportNo, $birthCertificateNo, $phone, $otherIdNo)
    {
        $path = "https://ecuadorims.iom.int/beneficiaries/show/";
        $query = <<<SQL
                declare @bnf_id bigint = :id,
                @full_name nvarchar(250) = :full_name,
                @national_id_no nvarchar(255) = :national_id_no,
                @passport_no nvarchar(255) = :passport_no,
                @birth_certificate_no nvarchar(255) = :birth_certificate_no,
                @phone_number nvarchar(255) = :phone_number,
                @other_id_no nvarchar(255) = :other_id_no

                select 
                    b.code,
                    CONCAT(
                        case when b.full_name = @full_name then '<li>Nombres y apellidos ' + b.full_name + ' esta duplicado con IOM ID <a href="$path' + CAST(b.id as nvarchar(255)) + '" target="_blank">' + b.code + '</a> - ' + u.name + ' - ' + gov.name + '</li>' end,
                        case when b.national_id_no = @national_id_no then '<li>Cedula ' + b.national_id_no + ' esta duplicado con IOM ID <a href="$path' + CAST(b.id as nvarchar(255)) + '" target="_blank">' + b.code + '</a> - ' + u.name + ' - ' + gov.name + '</li>' end,
                        case when b.passport_no = @passport_no then '<li>Pasaporte ' + b.passport_no + ' esta duplicado con IOM ID <a href="$path' + CAST(b.id as nvarchar(255)) + '" target="_blank">' + b.code + '</a> - ' + u.name + ' - ' + gov.name + '</li>' end,
                        case when b.other_id_no = @other_id_no then '<li>Otro documento de identidad ' + b.other_id_no + ' esta duplicado con IOM ID <a href="$path' + CAST(b.id as nvarchar(255)) + '" target="_blank">' + b.code + '</a> - ' + u.name + ' - ' + gov.name + '</li>' end,
                        case when b.other_id_no = @national_id_no then '<li>Otro documento de identidad ' + b.other_id_no + ' esta duplicado con de IOM ID <a href="$path' + CAST(b.id as nvarchar(255)) + '" target="_blank">' + b.code + '</a> - ' + u.name + ' - ' + gov.name + '</li>' end,
                        case when b.national_id_no = @other_id_no then '<li>Otro documento de identidad ' + b.national_id_no + ' esta duplicado con  IOM ID <a href="$path' + CAST(b.id as nvarchar(255)) + '" target="_blank">' + b.code + '</a> - ' + u.name + ' - ' + gov.name + '</li>' end,
                        case when b.birth_certificate_no = @birth_certificate_no then '<li>Certificado de nacimiento ' + b.birth_certificate_no + ' esta duplicado con IOM ID <a href="$path' + CAST(b.id as nvarchar(255)) + '"target="_blank">' + b.code + '</a> - ' + u.name + ' - ' + gov.name + '</li>' end,
                        case when b.phone_number = @phone_number then '<li>Telefono ' + b.phone_number + ' esta duplicado con IOM ID <a href="$path' + CAST(b.id as nvarchar(255)) + '"target="_blank">' + b.code + '</a> - ' + u.name + ' - ' + gov.name + '</li>' end
                    ) as error,
                    1 as is_warning
                from beneficiaries b
                left join governorates gov on gov.id = b.province
                left join units u on u.id = b.unit_id
                where
                (
                    (isnull(@full_name,'') != '' and b.full_name = @full_name) or 
                    (isnull(@national_id_no,'') != '' and b.national_id_no = @national_id_no and b.national_id_no != 'No disponible') or
                    (isnull(@passport_no,'') != '' and b.passport_no = @passport_no) or
                    (isnull(@other_id_no,'') != '' and b.other_id_no = @other_id_no) or
                    (isnull(@other_id_no,'') != '' and b.national_id_no = @other_id_no) or
                    (isnull(@national_id_no,'') != '' and b.other_id_no = @national_id_no and b.national_id_no != 'No disponible') or
                    (isnull(@birth_certificate_no,'') != '' and b.birth_certificate_no = @birth_certificate_no) or
                    (isnull(@phone_number,'') != '' and b.phone_number = @phone_number and b.phone_number != '0000000000') 
                ) and b.id != @bnf_id

                union 

                select b.code,
                    CONCAT(
                        case when fa.full_name = @full_name then '<li>Nombres y apellidos ' + fa.full_name + ' esta duplicado con el miembro familiar IOM ID <a href="$path' + CAST(b.id as nvarchar(255)) + '"target="_blank">' + b.code + '</a> - ' + u.name + ' - ' + gov.name + '</li>' end,
                        case when fa.family_national_id = @national_id_no then '<li>Cedula ' + fa.family_national_id + ' esta duplicado con el miembro familiar IOM ID <a href="$path' + CAST(b.id as nvarchar(255)) + '"target="_blank">' + b.code + '</a> - ' + u.name + ' - ' + gov.name + '</li>' end ,  
                        case when fa.family_national_id = @other_id_no then '<li>Otro documento de identidad ' + fa.family_national_id + ' esta duplicado con el miembro familiar IOM ID <a href="$path' + CAST(b.id as nvarchar(255)) + '"target="_blank">' + b.code + '</a> - ' + u.name + ' - ' + gov.name + '</li>' end    

                    ) as error,
                    1 as is_warning  
                from beneficiaries b
                left join beneficiaries_family_information fa on fa.parent_id = b.id
                left join governorates gov on gov.id = b.province
                left join units u on u.id = b.unit_id
                where
                (
                    (isnull(@full_name,'') != '' and fa.full_name = @full_name) or 
                    (isnull(@national_id_no,'') != '' and fa.family_national_id = @national_id_no and @national_id_no != 'no disponible') or
                    (isnull(@other_id_no,'') != '' and fa.family_national_id = @other_id_no and @national_id_no != 'no disponible') 
                ) and b.id != @bnf_id
SQL;

        $this->db->query($query);
        $this->db->bind(':id', $id);
        $this->db->bind(':full_name', $fullName);
        $this->db->bind(':national_id_no', $nationalIdNo);
        $this->db->bind(':passport_no', $passportNo);
        $this->db->bind(':other_id_no', $otherIdNo);
        $this->db->bind(':birth_certificate_no', $birthCertificateNo);
        $this->db->bind(':phone_number', $phone);

        $results = $this->db->resultSet();
        return $results;
    }
}
?>
