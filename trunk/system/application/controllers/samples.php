<?php

class Samples extends MY_Controller
{
    /**
     * Loads a page listing samples.
     *
     * @return void
     **/
    function index()
    {
        $query = $this->input->post('query');
        
        // Pagination url: samples/index/sort_by/sort_dir/page
        //     URI number:     1   /  2  /   3   /    4   / 5
        //       Defaults: samples/index/  name  /  desc  / 0

        // set database pagination settings
        $sort_by = $this->uri->segment(3,'name');
        $sort_dir = strtolower($this->uri->segment(4,'ASC'));
        $page = $this->uri->segment(5,0);
        $num_per_page = 20;
        $samples = Doctrine_Query::create()
            ->from('Sample')
            ->orderBy("$sort_by $sort_dir")
            ->limit($num_per_page)
            ->offset($page)
            ->execute();
            
        $nrows = Doctrine::getTable('Sample')->count();
        $alt_sort_page = $nrows - $page - $num_per_page;
        if ($alt_sort_page < 0) {
            $alt_sort_page = 0;
        }

        // set pagination options
        $this->load->library('pagination');
        $config['base_url'] = site_url("samples/index/$sort_by/$sort_dir");
        $config['total_rows'] = $nrows;
        $config['per_page'] = $num_per_page;
        $config['uri_segment'] = 5;
        $this->pagination->initialize($config);
            
        $data = array(
            'title'        => 'Manage Samples',
            'main'         => 'samples/index',
            'samples'      => $samples,
            'pagination'   => 'Go to page: ' . $this->pagination->create_links(),
            'sort_by'      => $sort_by,
            'alt_sort_dir' => switch_sort($sort_dir),
            'page'         => $page,
            'alt_sort_page' => $alt_sort_page,
            'extraHeadContent' => 
                '<script type="text/javascript" src="js/sample_search.js"></script>',
        );
    
        $this->load->view('template', $data);
    }

    /**
     * Displays the edit form and evaluates submits. If the submit validates properly, 
     * it makes change to sample in database and redirects.
     *
     */
    function edit($id = 0) 
    {
        $is_refresh = $this->input->post('is_refresh');

        if ($id) {
            // edit an existing sample
            $sample = Doctrine_Query::create()
                ->from('Sample s, s.Project p')
                ->where('s.id = ?', $id)
                ->fetchOne();
                
            if (!$sample)
            {
                // couldn't find the sample, so we 404 (probably change later)
                show_404('page');
            }
            
            if (isset($sample->Project)) $data->project = $sample->Project;
            
            $data->title = 'Edit Sample';
            $data->subtitle = 'Editing '.$sample->name;
            $data->arg = $id;
        } else {
            // create a new sample object
            $sample = new Sample();
            
            $data->title = 'Add Sample';
            $data->subtitle = 'Enter Sample Information:';
            $data->arg = '';
        }
        
        // generate some select boxes to associate projects with the sample
        $projOptions = array();
        if (isset($sample->Project)) {
            $nprojs = $sample->Project->count();
            for ($i = 0; $i < $nprojs; $i++) {
                $tmp = "<option>";
                $projOptions[] = '<option>';
            }
        }
        $projects = Doctrine::getTable('Project')->getList();
        $defaultSelect = '<option>';
        foreach ($projects as $p) {
            $defaultOption = "<option value=$p->id>$p->name";
            $selected = "<option value=$p->id selected>$p->name";
            if ($nprojs) {
                for ($i = 0; $i < $nprojs; $i++) {
                    if ($sample['Project'][$i]['id'] == $p['id']) {
                        $projOptions[$i] .= $selected;
                    } else {
                        $projOptions[$i] .= $defaultOption;
                    }
                }
            }
            $defaultSelect .= $defaultOption;
        }
        $data->defaultSelect = $defaultSelect;
        $data->projOptions = $projOptions;
        
        // set up some javascript to add more project select boxes
        $data->extraHeadContent = <<<EHC
            <script type="text/javascript">
            var options='<select name="proj[]">$defaultSelect</select><br/>';
            $(document).ready(function() {
                $("#add_select").click(function(event) {
                    event.preventDefault();
                    $(options).insertBefore("#add_select");
                });
            });
            </script>
EHC;
        
        if ($is_refresh) {
            // validate what was submitted
            $valid = $this->form_validation->run('samples');
            $sample->merge($this->input->post('sample'));
            $proj = $this->input->post('proj');
            
            if ($proj) {
                $proj = array_unique($proj);
            }
            
            if ($valid) {
                $sample->merge($this->input->post('sample'));
                $sample->unlink('Project');
                $sample->save();
                $off = 0;
                for ($i = 0; $i < count($proj); $i++) {
                    if ($proj[$i] == '') {
                        $off++;
                        continue;
                    }
                    $sample['ProjectSample'][$i-$off]['project_id'] = $proj[$i];
                    $sample['ProjectSample'][$i-$off]['sample_id'] = $sample->id; 
                }
                $sample->save();
                redirect('samples/edit/'.$sample->id);
            }
        }

        $data->sample = $sample;
        $data->main = 'samples/edit';
        $this->load->view('template', $data);
    }
    
    /**
     * Shows the data for a sample.
     *
     * @return void
     */
    function view($id)
    {
        $sample = Doctrine_Query::create()
            ->from('Sample s, s.Project')
            ->where('s.id = ?', $id)
            ->fetchOne();
        
        if ( ! $sample) {
            show_404('page');
        }
        
        if (isset($sample->Project)) $data->projects = $sample->Project;
        $data->title = 'View Sample';
        $data->subtitle = 'Viewing '.$sample->name;
        $data->arg = $id;
        $data->sample  = $sample;
        $data->main  = 'samples/view';
        $this->load->view('template', $data);
    }
    
    function search_names()
    {
        $q = $this->input->post('q');
        
        if (!$q) return;
        
        $samples = Doctrine_Query::create()
               ->from('Sample s')
               ->select('s.name')
               ->where('s.name LIKE ?', "%$q%")
               ->execute();
        
        if (!$samples) return;
        
        foreach ($samples as $s) {
            echo "$s->name\n";
        }
    }

}

/* End of file samples.php */
/* Location: ./system/application/controllers/samples.php */