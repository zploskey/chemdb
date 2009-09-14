<div style="width: 800px;">
    <hr>
    <div style="height: 128px; display: relative;">
        <span style="float:right;width:200px;">
            <img src="img/logo.jpeg">
        </span>
        <span style="position: relative; top: 30%;">
            <i><h1><?=$title?></h1></i>
        </span>
    </div>
    <hr>
    <b><em>You are logged in as: 
        <?=htmlentities($this->input->server('REMOTE_USER'))?>
    </em></b>
    <hr>
</div>