<?php
class MY_Form_validation extends CI_Form_validation
{
    /**
     * Determines if $value already exists in the table specified in $field.
     * $field is formatted as 'table_name.field_name'
     *
     * @param string $value value to check for uniqueness in DB
     * @param string $field the database field to check
     * @return bool $id true if the value is unique
     */
    function is_unique($value, $field, $id)
    {
        if (isset($id) AND ($id <= 0)) {
            return false;
        }

        list($table, $column) = explode('.', $field);

        $result = Doctrine_Query::create()
            ->select('id')
            ->from($table)
            ->where("$column = ?", $value)
            ->execute();
        
        if ($result->count() == 0) {
            return true; // it's a unique name
        } else {
            // There was a match, find out if it is just the submitted item or
            // a dupe. Assuming that the database had just 1 match, this loop
            // should only execute once, but let's be safe just in case.
            foreach ($result as $row) {
                if ($row->id != $id) {
                    // id mismatch, this would have created a multiple
                    return false;
                }
            }
            // this is an edit, let it pass
            return true;
        }
    }

    function valid_date($date) {
        $tdate = trim($date);
        if (! preg_match('/^\d\d\d\d[-]\d\d[-]\d\d$/', $tdate) ) {
            return false;
        }
        list($year, $month, $day) = explode('-', $tdate);
        return checkdate($month, $day, $year);
    }
    
    function alpha_dot_dash($val)
    {
        return (!preg_match("/^([\.-a-z0-9_-])+$/i", $val)) ? FALSE : TRUE;
    }
}
