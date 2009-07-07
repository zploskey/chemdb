<?php
class MY_Loader extends CI_Loader{
    /**
     * @addtogroup IgnitedRecord
     * @{
     */
    /* 
     * Copyright (c) 2008, Martin Wernstahl
     * All rights reserved.
     *
     * Redistribution and use in source and binary forms, with or without
     * modification, are permitted provided that the following conditions are met:
     *     * Redistributions of source code must retain the above copyright
     *       notice, this list of conditions and the following disclaimer.
     *     * Redistributions in binary form must reproduce the above copyright
     *       notice, this list of conditions and the following disclaimer in the
     *       documentation and/or other materials provided with the distribution.
     *     * The name of Martin Wernstahl may not be used to endorse or promote products
     *       derived from this software without specific prior written permission.
     *
     * THIS SOFTWARE IS PROVIDED BY Martin Wernstahl ``AS IS'' AND ANY
     * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
     * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
     * DISCLAIMED. IN NO EVENT SHALL Martin Wernstahl BE LIABLE FOR ANY
     * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
     * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
     * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
     * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
     * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
     * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
     */
    /**
     * Loads the IgnitedRecord class and the Model class, also acts as a wrapper for Loader::model().
     * 
     * This enables an other way to instantiate IgnitedRecord's child classes:
     * @code
     * $this->load->ORM();
     * $this->load->model('a_child_class');
     * // or as a wrapper for model()
     * $this->load->ORM('a_child_class','pages');
     * @endcode
     * 
     * @param $model The model name of the model to load, if false ORM() will not load any derived classes (models)
     * @param $name The name parameter is passed to the model() function, determining if the property name should differ from the default
     * @param $db_conn The database connection passed to the model() function
     * 
     * @todo Check if db is loaded
     */
    function ORM($model = false, $name = '', $db_conn = FALSE){
        if(!class_exists('IgnitedRecord'))
        {
            // change this line if the IgnitedRecord file is stored elsewhere
            require_once(APPPATH.'libraries/ignitedrecord/ignitedrecord.php');
        }
        
        if($model != false)
        {
            $this->model($model, $name, $db_conn);
        }
    }
    /**
     * @}
     */
}
?>