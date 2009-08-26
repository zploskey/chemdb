<html>
    <head>
        <title>UW-CNL-DB</title>
        <style>
        <!--
        table {
            empty-cells:show;
        }
        .arial10
            {font-family:Arial;
            font-size:10.0pt;}
        .arial8
            {font-family:Arial;
            font-size:8.0pt;}
        .arial14
            {font-family:Arial;
            font-size:14.0pt;}
        .arial12
            {font-family:Arial;
            font-size:12.0pt;}
        -->
        </style>
    </head>

    <body>
        <table width="800" class="arial8">
            <tr>
                <td colspan="4" width="400">
                    <h3>Batch information:</h3>
                    Batch ID: <?php echo $batch->id; ?><br/>
                    Batch start date: <?php echo $batch->start_date; ?><br/>
                    Batch owner: <?php echo $batch->owner; ?><br/>
                    Batch description: <?php echo $batch->description; ?><br/>
                    Logged in as: <?php echo htmlentities($_SERVER['REMOTE_USER']); ?>
                </td>
            </tr>
        </table>

        <table width="800" border="1" cellspacing="0" class="arial8">
            <tr>
                <td>
                </td>
                <td align="center" colspan="3">
                    Cation wts (mg)
                </td>
                <td colspan="2"></td>
                <td align="center" colspan="6">
                    Beaker numbers:
                </td>
            </tr>
            <tr>
                <td>Sample name</td>
                <td align="center">Al</td>
                <td align="center">Fe</td>
                <td align="center">Ti</td>
                <td align="center">Dissolution<br>bottle</td>
                <td align="center">ml HF</td>
                <td align="center">Split<br>bkr 1</td>
                <td align="center">Split<br>bkr 2</td>
                <td align="center">Dry-down<br>vessel</td>
                <td align="center">Anion<br>column<br>eluent</td>
                <td align="center">Final<br>Be<br>fraction</td>
                <td align="center">Final<br>Al</br>fraction</td>
            </tr>

        <?php for ($i = 0; $i < $batch->Analysis->count(); $i++): ?>
            <tr>
                <td><?php echo $batch->Analysis[$i]->sample_name; ?></td>

                <?php if ($tmpa[$i]['inAlDb']): ?>
                    <td align="center"><?php echo $tmpa[$i]['tot_al']; ?></td>
                    <td align="center"><?php echo $tmpa[$i]['tot_fe']; ?></td>
                    <td align="center"><?php echo $tmpa[$i]['tot_ti']; ?></td>
                <?php else: ?>
                    <td colspan="3" align="center">Not in Al-check db</td>
                <?php endif; ?>
                <td align="center">
                    <?php echo $batch->Analysis[$i]->DissBottle->bottle_number; ?>
                </td>
                <td align="center">
                    <?php echo $tmpa[$i]['mlHf']; ?>
                </td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>

        <?php endfor; ?>

        </table>

        <!-- Dissolution -->

        <p/>

        <table width="480" height="60" border="1" cellspacing="0" class="arial8">
            <tr><td colspan="6">Dissolution:</td></tr>
            <tr>
                <td width="80" align="center">HF<br>added</td>
                <td width="80" align="center">Vented<br>cap</td>
                <td width="80" align="center">Dissolved</td>
                <td width="80" align="center">Solid<br>cap</td>
                <td width="80" align="center">Weighed</td>
                <td width="80" align="center">Mixed</td>
            </tr>
        </table>

        <!-- Splitting -->

        <p/>


        <table width="400" height="60" border="1" cellspacing="0" class="arial8">
            <tr><td colspan="5">Splitting:</td></tr>
            <tr>
                <td width="80" align="center">H2SO4<br>added</td>
                <td width="80" align="center">Splits<br>dried 1X</td>
                <td width="80" align="center">H2O added</td>
                <td width="80" align="center">Splits<br>dried  2X</td>
                <td width="80" align="center">8 ml<br>1% HNO3</td>
            </tr>
        </table>


        <!-- Drydown -->

        <p/>

        <table width="480" height="60" border="1" cellspacing="0" class="arial8">
            <tr><td colspan="6">Dry-down and chloride conversion:</td></tr>
            <tr>
                <td width="80" align="center">0.5 ml<br>8M HNO3</td>
                <td width="80" align="center">3 ml<br>6M HCl</td>
                <td width="80" align="center">Dried to<br>fluoride</td>
                <td width="80" align="center">6M HCl</td>
                <td width="80" align="center">6M HCl</td>
                <td width="80" align="center">6M HCl</td>
            </tr>
        </table>

        <!-- Anion exchange -->

        <p/>

        <table width="500" height="70" border="1" cellspacing="0" class="arial8">
            <tr><td colspan="5">Anion exchange:</td></tr>
            <tr><td colspan="2" align="center">Column prep</td><td colspan="3" align="center">Run</td></tr>
            <tr>
                <td width="100" align="center">Clean resin<br>1.2M HCl</td>
                <td width="100" align="center">Condition<br>6M HCl</td>
                <td width="100" align="center">Load<br>sample</td>
                <td width="100" align="center">Rinse</td>
                <td width="100" align="center">Elute<br>6M HCl</td>
            </tr>
        </table>

        <!-- Sulfate conversion -->

        <p/>

        <table width="560" height="60" border="1" cellspacing="0" class="arial8">
            <tr><td colspan="7">Sulfate conversion:</td></tr>
            <tr>
                <td width="80" align="center">1 ml<br>0.5M<br>H2SO4</td>
                <td width="80" align="center">Dry</td>
                <td width="80" align="center">H202<br>H20</td>
                <td width="80" align="center">Dry</td>
                <td width="80" align="center">H202<br>H20</td>
                <td width="80" align="center">Dry</td>
                <td width="80" align="center">2 ml H20<br>+ H2O2</td>
            </tr>
        </table>

        <!-- Cation exchange -->

        <p/>

        <table width="640" height="75" border="1" cellspacing="0" class="arial8">
            <tr><td colspan="8">Cation exchange:</td></tr>
            <tr>
                <td colspan="3" align="center">Column prep</td>
                <td colspan="5" align="center">Run</td>
            </tr>
            <tr>
                <td width="80" align="center">Clean resin<br>3M HCl</td>
                <td width="80" align="center">Condition<br>1.2M HCl</td>
                <td width="80" align="center">Condition<br>0.25M H2SO4</td>
                <td width="80" align="center">Load<br>sample</td>
                <td width="80" align="center">Rinse<br>1 ml 0.5M<br/>H2SO4</td>
                <td width="80" align="center">Elute Ti<br>12 ml<br>0.5M H2SO4</td>
                <td width="80" align="center">Elute Be<br>10 ml<br>1.2M HCl</td>
                <td width="80" align="center">Elute Al<br>6 ml<br>3M HCl</td>
            </tr>
        </table>

        <!-- Recovery -->

        <p/>

        <table width="400" height="75" border="1" cellspacing="0" class="arial8">
            <tr><td colspan="4">Recovery:</td></tr>
            <tr>
                <td width="100" align="center">Be fraction:<br>5 drops<br>8M HNO3</td>
                <td width="100" align="center">Al fraction:<br>0.5 ml<br>8M HNO3</td>
                <td width="100" align="center">Dry-down</td>
                <td width="100" align="center">Recovery:<br/>4 ml 1% HNO3</td>
            </tr>
        </table>

    </body>

</html>