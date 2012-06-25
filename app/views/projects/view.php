<?=$this->load->view('projects/nav')?>
<p><h2><?=$subtitle?> (<?=anchor('projects/edit/'.$proj->id, 'Edit')?>)</h2></p>
<br>
<div class="data">
    <table>
        <tr>
            <td align="right"><em>Name:&nbsp;</em></td>
            <td><?=$proj->name?></td>
        </tr>
        <tr>
            <td align="right"><em>Date Added:&nbsp;</em></td>
            <td><?=$proj->date_added?></td>
        </tr>
        <tr>
            <td align="right"><em>Description:&nbsp;</em></td>
            <td><?=$proj->description?></td>
        </tr>
    </table>
    <br><br>
    <div class="data">
    <p>
        <table class="itemlist">
            <tr>
                <th>Associated Samples &nbsp;&nbsp;&nbsp;</th>
                <th>Actions</th>
            </tr>
            <? if ($proj->Sample->count()): ?>
                <? foreach ($proj->Sample as $s): ?>
                    <tr>
                        <td><?=$s->name?></td>
                        <td>
                            <span id="actionbar">
                                <ul>
                                    <li><?=anchor('samples/view/'.$s->id, 'View')?></li>
                                    <li><?=anchor('samples/edit/'.$s->id, 'Edit')?></li>
                                </ul>
                            </span>
                        </td>
                    </tr>
                <? endforeach;?>
            <? else: ?>
                <tr><td>None</td><td></td></tr>
            <? endif; ?>
        </table>
    </p>
</div>