<div class="form-container">  
    <?php if(count($tr)>0): ?>
        <table class="table" id="_verifiedgc">
            <thead>
                <tr>
                    <th>Textfile Line</th>       
                    <th>Credit Limit</th>   
                    <th>Cred. Pur. Amt + Add-on</th>    
                    <th>Add-on Amt</th>      
                    <th>Remaining Balance</th>
                    <th>Transaction #</th>
                    <th>Time of Cred Tranx</th>
                    <th>Bus. Unit</th>
                    <th>Terminal #</th>
                    <th>Ackslip #</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tr as $t): ?>
                    <tr>
                        <td><?php echo $t->seodtt_line; ?></td>
                        <td><?php echo $t->seodtt_creditlimit; ?></td>
                        <td><?php echo $t->seodtt_credpuramt; ?></td>
                        <td><?php echo $t->seodtt_addonamt; ?></td>
                        <td><?php echo $t->seodtt_balance; ?></td>
                        <td><?php echo $t->seodtt_transno; ?></td>
                        <td><?php echo $t->seodtt_timetrnx; ?></td>
                        <td><?php echo $t->seodtt_bu; ?></td>
                        <td><?php echo $t->seodtt_terminalno; ?></td>
                        <td><?php echo $t->seodtt_ackslipno; ?></td>                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

