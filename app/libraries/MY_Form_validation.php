<?php

class MY_Form_validation extends CI_Form_validation
{
    /**
     * Use the php function is_numeric as a validator, allowing us to validate
     * numbers in scientific notation.
     *
     * @param string $str
     * @return bool
     */
    public function is_numeric($str)
    {
        return is_numeric($str);
    }

    /**
     * Determines if $value already exists in the $column of $table.
     *
     * @param string $value value to check for uniqueness in DB
     * @param string $table the Doctrine table containing $column
     * @param string $column the column to check
     * @param int $id the
     * @return bool $id true if the value is unique
     */
    public function is_unique_or_existing($value, $table, $column, $id)
    {
        if (isset($id) and ($id <= 0)) {
            return false;
        }
        $result = Doctrine_Query::create()
            ->from($table)
            ->select('id')
            ->where("$column = ?", $value)
            ->execute();

        if ($result->count() == 0) {
            return true; // it's a unique name
        }
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

    public function valid_date($date)
    {
        $tdate = trim($date);

        if (!preg_match('/^\d\d\d\d[-]\d\d[-]\d\d$/', $tdate)) {
            $this->set_message(
                __FUNCTION__,
                'The %s field must be of the form YYYY-MM-DD.'
            );
            return false;
        }

        list($year, $month, $day) = explode('-', $tdate);
        $validDate = checkdate($month, $day, $year);

        if ($validDate) {
            return true;
        }

        $this->set_message(__FUNCTION__, 'The %s field is not a valid date');
        return false;
    }

    public function alpha_dot_dash($val)
    {
        return preg_match('/^[\.-a-z0-9_-]+$/i', $val) ? true : false;
    }

    public function negis0($val)
    {
        if (is_numeric($val) && $val < 0) {
            return '0.0';
        }
        return $val;
    }

    /*
     * Returns true if value is not greater than 180.
     */
    public function latlong($val)
    {
        return is_numeric($val) && abs($val) <= 180;
    }

    public function shield_factor($val)
    {
        return is_numeric($val) && abs($val) <= 1;
    }
}
