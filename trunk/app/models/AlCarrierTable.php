<?php
/**
 * Accesses the aluminum carrier table.
 */
class AlCarrierTable extends Doctrine_Table
{
    /**
     * Generates an html option list containing carrier names and values of
     * the corresponding carrier id.
     * @return string $options
     */
    public function getSelectOptions($carrier_id)
    {
        $carriers = $this->createQuery('c')
            ->select('c.id, c.name')
            ->orderBy('c.in_service_date DESC')
            ->execute();

        $options = '';
        foreach ($carriers as $c) {
            $options .= "<option value=$c->id";
            if ($c->id == $carrier_id) {
                $options .= ' selected';
            }
           $options .= "> $c->name";
        }

        return $options;
    }
}