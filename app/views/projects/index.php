<?=$this->load->view('projects/nav')?>
<div class="pagination">
    <?
    if ($paginate) {
        $pagstring = "Go to page: $pagination";
        echo $pagstring;
    } ?>
</div>
<div class="data">
    <p>
        <table class="itemlist">
            <tr>
                <th>
                    <?=anchor("projects/index/$sort_by/$alt_sort_dir/$alt_sort_page", 'Name')?>
                </th>
                <th>Actions</th>
            </tr>

            <?php foreach($projects as $p): ?>

                <tr>
                    <td id="name"><?=$p->name?></td>
                    <td>
                        <span id="actionbar">
                            <ul>
                                <li><?=anchor('projects/view/'.$p->id, 'View')?></li>
                                <li><?=anchor('projects/edit/'.$p->id, 'Edit')?></li>
                            </ul>
                        </span>
                    </td>
                </tr>

            <?php endforeach; ?>

        </table>
    </p>
</div>
<div class="pagination">
    <? if ($paginate) echo $pagstring; ?>
</div>