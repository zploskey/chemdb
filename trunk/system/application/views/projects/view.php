<div id="navbar">
    <ul>
        <li><?=anchor('welcome','Return to Main Menu')?> |</li>
        <li><?=anchor('projects','Projects List')?></li>
    </ul>
</div>
<br>
<p><h2><?=$subtitle?> (<?=anchor('projects/edit/'.$proj->id, 'Edit')?>)</h2></p>
<br>
<div class="data">
    <table>
        <tr>
            <td align="right"><em>Name:</em></td>
            <td><?=$proj->name?></td>
        </tr>
        <tr>
            <td align="right"><em>Date Added:</em></td>
            <td><?=$proj->date_added?></td>
        </tr>
        <tr>
            <td align="right"><em>Description:</em></td>
            <td><?=$proj->description?></td>
        </tr>
    </table>
    <br><br>
    <div class="data">
    <p>
        <table class="itemlist">
            <tr>
                <th>Associated Samples</th>
                <th>Actions</th>
            </tr>
            <? foreach ($samples as $s): ?>
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
        </table>
    </p>
</div>