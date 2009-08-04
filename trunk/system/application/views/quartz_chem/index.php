<table width="800">
    <?=$this->load->view('tr_main_link_hr')?>
    <tr><td colspan="2"><hr></td></tr>
    <!--- CREATE A NEW BATCH -->
    <?=form_open('quartz_chem/new_batch')?>
        <tr>
            <td><b><i>Create a new batch</i></b></td>
            <td align="center">
                <input type=submit width="40" value="Create a new batch">
            </td>
        </tr>
    <?=form_close()?>
    
    <tr><td colspan="2"><hr></td></tr>
    <p/>
    <!--- SAMPLE LOADING AND CARRIER ADDITION -->
    
    <tr>
        <td colspan="2"><i><b>Sample loading and carrier addition</b></i></td>
    </tr>
    
    <?=form_open(site_url('quartz_chem/load_samples'))?>
        <tr>
            <td align="center">
                <i>Batch:</i>
                <select name="batch_id">
                    <?=$open_batches?>
                </select>
            </td>
            <td align="center">
                <input type="submit" value="Sample loading and carrier addition">
            </td>
        </tr>
    <?=form_close()?>
    
    <tr><td colspan="2"><hr/></td></tr>
    <p/>
    
    <!--- PRINT THE TRACKING SHEET -->
    
    <tr>
        <td colspan="2"><i><b>Print lab tracking sheet</b></i></td>
    </tr>
    <?=form_open('quartz_chem/print_tracking_sheet')?>
        <tr>
            <td align="center">
                <i>Batch:</i>
                <select name="batch_id">
                    <?=$open_batches?>
                </select>
            </td>
            <td align="center">
                <input type=submit value="Print tracking sheet for this batch">
            </td>
        </tr>
    <?=form_close()?>
    <tr><td colspan="2"><hr></td></tr>
    <p/>
    
    <!--- ADD TOTAL SOLUTION WEIGHTS -->
    
    <tr>
        <td colspan="2"><i><b>Add total solution weights</b></i></td>
    </tr>
    <?=form_open('quartz_chem/add_solution_weights')?>
        <tr>
            <td align="center">
                <i>Batch:</i>
                <select name="batch_id">
                    <?=$open_batches?>
                </select>
            </td>
            <td align="center">
                <input type=submit value="Add total solution weights">
            </td>
        </tr>
    <?=form_close()?>
    <tr><td colspan="2"><hr></td></tr>
    <p/>
    
    <!--- ADD SPLIT WEIGHTS -->
    
    <tr>
        <td colspan="2">
            <b><i>Add split weights</i></b>
        </td>
    </tr>
    <?=form_open('quartz_chem/add_split_weights')?>
        <tr>
            <td align="center">
                <i>Batch:</i>   
                <select name="batch_id">
                    <?=$open_batches?>
                </select>
            </td>
            <td align="center">
                <input type=submit value="Add split weights">
            </td>
        </tr>
    <?=form_close()?>
    <tr><td colspan="2"><hr></td></tr><p/>
    
    <!--- ADD ICP WEIGHTS -->
    
    <tr>
        <td colspan="2">
            <i><b>Add ICP solution weights</b></i>
        </td>
    </tr>
    
    <?=form_open('quartz_chem/add_icp_weights')?>
        <tr>
            <td align="center">
                <i>Batch:</i>   
                <select name="batch_id">
                    <?=$open_batches?>
                </select>
            </td>
            <td align="center">
                <input type=submit value="Add ICP solution weights">
            </td>
        </tr>
    <?=form_close()?>
    
    <tr><td colspan="2"><hr></td></tr><p/>
    
    <!--- ADD ICP RESULTS -->
    
    <tr>
        <td colspan="2">
            <i><b>Enter ICP results</b></i>
        </td>
    </tr>
    
    <?=form_open('quartz_chem/add_icp_results')?>   
        <tr>
            <td align="center">
                <i>Batch:</i>   
                <select name="batch_id">
                    <?=$open_batches?>
                </select>
            </td>
            <td align="center">
                <input type=submit value="Add ICP results">
            </td>
        </tr>
    <?=form_close()?>
    <tr><td colspan="2"><hr></p></td></tr>
    
    <!-- ICQ QUALITY CONTROL -->
    <tr>
        <td colspan="2">
            <i><b>ICP Quality Control</b></i>
        </td>
    </tr>
    
    <?=form_open('quartz_chem/icp_quality_control')?>   
        <tr>
            <td align="center">
                <i>Batch:</i>   
                <select name="batch_id">
                    <?=$open_batches?>
                </select>
            </td>
            <td align="center">
                <input type="submit" value="Check ICP results">
            </td>
        </tr>
    <?=form_close()?>
    <tr><td colspan="2"><hr></p></td></tr>
    
    <!--- FINAL REPORT -->
    
    <tr>
        <td colspan="2">
            <i><b>Final report</b></i>
        </td>
    </tr>
    
    <?=form_open('quartz_chem/final_report')?>
        <tr>
            <td align="center">
                <i>Batch:</i>
                <select name="batch_id">
                    <?=$all_batches?>
                </select>
            </td>
            <td align="center">
                <input type=submit value="Get final report for this batch">
            </td>
        </tr>
    <?=form_close()?>
    <tr><td colspan="2"><hr></td></tr>
    
    <tr>
        <td colspan="2">
            <i><b>Take a completed batch off the active list</b></i>
        </td>
    </tr>
    
    <?=form_open('quartz_chem/index')?>
        <input type="hidden" name="is_lock" value="true">
        <tr>
            <td align="center">
                <i>Batch:</i>
                <select name="batch_id">
                    <?=$open_batches?>
                </select>
            </td>
            <td align="center">
                <input type=submit value="This batch is done!">
            </td>
        </tr>
    <?=form_close()?>
    
    <tr><td colspan="2" ><hr></td></tr>
</table>

<br><br><br><br>