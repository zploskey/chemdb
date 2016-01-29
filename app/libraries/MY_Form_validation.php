<?php

class MY_Form_validation extends CI_Form_validation
{
    /**
     * Determines if $value already exists in the table specified in $field.
     * $field is formatted as 'table_name.field_name'
     *
     * @param string $value value to check for uniqueness in DB
     * @param string $field the database field to check
     * @param int 
     * @return bool $id true if the value is unique
     */
    function is_unique_or_existing($value, $field, $id)
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

    function noreq_numeric($val)
    {
        if ($val == '' || is_numeric($val)) {
            return true;
        }

        $this->set_message(__FUNCTION__, 'The %s field must be a number.');
        return false;
    }

    function noreq_abs($val)
    {
        if ($val != '' && is_numeric($val)) {
            return abs($val);
        }
        return $val;
    }

    function negis0($val)
    {
        if (is_numeric($val) && $val < 0) {
            return '0.0';
        }
        return $val;
    }

    function noreq_natural_no_zero($val)
    {
        return ($val == '' || $this->is_natural_no_zero($val));
    }

    function _is_unique($val, $field)
    {
        $id = $this->uri->segment(3, null);

        if ($this->is_unique_or_existing($val, $field, $id)) {
            return true;
        }
        $this->set_message(__FUNCTION__,
            'The %s field already exists in the database, please choose a different one.');
        return false;
    }

    function noreq_alpha_dot_dash($val)
    {
        return ($val == '' || $this->alpha_dot_dash($val));
    }

    /*
     * Returns true if value is not greater than 180.
     */
    function latlong($val)
    {
        return (is_numeric($val) && abs($val) <= 180);
    }

    function noreq_latlong($val)
    {
        return ($val == '' || $this->latlong($val));
    }

    function shield_factor($val)
    {
        return (is_numeric($val) && abs($val) <= 1);
    }

    function noreq_shield_factor($val)
    {
        return ($val == '' || $this->shield_factor($val));
    }
}
